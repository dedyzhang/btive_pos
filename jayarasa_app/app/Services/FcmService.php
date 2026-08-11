<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\Role;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Throwable;

class FcmService
{
    /**
     * Notify all registered admin devices that an order has been paid.
     */
    public function notifyPaymentSuccess(Transactions $transaction): void
    {
        // Push notifications are best-effort: nothing in here may ever block
        // payment finalization (missing table/migration, missing vendor
        // package, misconfigured credentials, network errors, etc.).
        try {
            $tokens = DeviceToken::whereIn('user_id', $this->adminUserIds())->pluck('fcm_token', 'id');

            if ($tokens->isEmpty()) {
                Log::info('FCM notifyPaymentSuccess: no admin device_tokens registered, skipping.');
                return;
            }

            $totalFormatted = 'Rp ' . number_format((float) $transaction->total, 0, ',', '.');

            // Data-only message (no `withNotification`): this guarantees our
            // FirebaseMessagingService.onMessageReceived() always runs — including when
            // the app is backgrounded or fully killed — so the click action reliably
            // opens the right page instead of falling back to the OS default launcher.
            $message = CloudMessage::new()
                ->withData([
                    'type' => 'payment_success',
                    'title' => 'Pembayaran Berhasil',
                    'body' => "Order {$transaction->invoice_number} - {$totalFormatted} telah dibayar",
                    'transaction_uuid' => $transaction->uuid,
                    'invoice_number' => (string) $transaction->invoice_number,
                    'total' => (string) $transaction->total,
                    'click_action' => route('activity.index', ['transaction' => $transaction->uuid]),
                ]);

            $this->sendToTokens($tokens, $message);
        } catch (Throwable $e) {
            Log::error('FCM notifyPaymentSuccess failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify all registered admin devices of today's total revenue so far. Meant to be
     * triggered by the scheduled `report:send-daily-revenue` command, but safe to call anytime.
     */
    public function notifyDailyRevenue(): void
    {
        try {
            $tokens = DeviceToken::whereIn('user_id', $this->adminUserIds())->pluck('fcm_token', 'id');

            if ($tokens->isEmpty()) {
                Log::info('FCM notifyDailyRevenue: no admin device_tokens registered, skipping.');
                return;
            }

            $paidToday = Transactions::where('status', 'paid')->whereDate('paid_at', now()->toDateString());
            $totalRevenue = (clone $paidToday)->sum('total');
            $transactionCount = (clone $paidToday)->count();

            $totalFormatted = 'Rp ' . number_format((float) $totalRevenue, 0, ',', '.');

            $message = CloudMessage::new()
                ->withData([
                    'type' => 'daily_revenue',
                    'title' => 'Laporan Pendapatan Hari Ini',
                    'body' => "Total pendapatan hari ini: {$totalFormatted} dari {$transactionCount} transaksi",
                    'total' => (string) $totalRevenue,
                    'transaction_count' => (string) $transactionCount,
                    'click_action' => route('activity.report'),
                ]);

            $this->sendToTokens($tokens, $message);
        } catch (Throwable $e) {
            Log::error('FCM notifyDailyRevenue failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify cashier and kitchen devices that a new order has come in (currently: a customer
     * self-order via QR code — the one case where neither of them created the order themselves).
     */
    public function notifyNewOrder(Transactions $transaction): void
    {
        try {
            $tokens = DeviceToken::whereIn('user_id', $this->cashierAndKitchenUserIds())->pluck('fcm_token', 'id');

            if ($tokens->isEmpty()) {
                Log::info('FCM notifyNewOrder: no cashier/kitchen device_tokens registered, skipping.');
                return;
            }

            $orderLabel = $transaction->order_type === 'take_away'
                ? 'Take Away'
                : ($transaction->table->name ?? 'Dine In');

            $message = CloudMessage::new()
                ->withData([
                    'type' => 'new_order',
                    'title' => 'Pesanan Baru Masuk',
                    'body' => "Pesanan baru dari {$orderLabel} ({$transaction->invoice_number})",
                    'transaction_uuid' => $transaction->uuid,
                    'invoice_number' => (string) $transaction->invoice_number,
                    'click_action' => route('kitchen.queue', ['transaction' => $transaction->uuid]),
                ]);

            $this->sendToTokens($tokens, $message);
        } catch (Throwable $e) {
            Log::error('FCM notifyNewOrder failed: ' . $e->getMessage());
        }
    }

    /**
     * device_tokens holds every logged-in role's device, but payment-success pushes are
     * only meant for admins. Resolve every user id that counts as "admin" — the literal
     * admin role plus any custom role granted the access_admin_dashboard permission.
     */
    private function adminUserIds()
    {
        $adminRoleNames = Role::all()
            ->filter(fn ($role) => in_array('access_admin_dashboard', $role->permissions ?? []))
            ->pluck('name');

        return User::where('role', 'admin')
            ->orWhereIn('role', $adminRoleNames)
            ->pluck('uuid');
    }

    /**
     * Resolve every user id with cashier or kitchen access — the literal 'cashier'/'kasir'/'dapur'
     * roles plus any custom role granted access_cashier or view_kitchen_queue.
     */
    private function cashierAndKitchenUserIds()
    {
        $roleNames = Role::all()
            ->filter(fn ($role) => in_array('access_cashier', $role->permissions ?? [])
                || in_array('view_kitchen_queue', $role->permissions ?? []))
            ->pluck('name')
            ->push('cashier', 'kasir', 'dapur')
            ->unique();

        return User::whereIn('role', $roleNames)->pluck('uuid');
    }

    private function sendToTokens($tokens, CloudMessage $message): void
    {
        try {
            $messaging = $this->makeMessaging();
        } catch (Throwable $e) {
            Log::warning('FCM not configured, skipping push notification: ' . $e->getMessage());
            return;
        }

        try {
            $report = $messaging->sendMulticast($message, $tokens->values()->all());
        } catch (Throwable $e) {
            Log::error('FCM sendMulticast failed: ' . $e->getMessage());
            return;
        }

        Log::info("FCM sendMulticast: {$report->successes()->count()} succeeded, {$report->failures()->count()} failed, out of {$tokens->count()} token(s).");

        // Drop tokens that are no longer valid (app uninstalled, token rotated, etc.)
        foreach ($report->invalidTokens() as $invalidToken) {
            DeviceToken::where('fcm_token', $invalidToken)->delete();
        }
    }

    private function makeMessaging()
    {
        $credentials = config('firebase.credentials');

        if (!$credentials || !file_exists($credentials)) {
            throw new \RuntimeException('Firebase service account credentials not found at ' . $credentials);
        }

        return (new Factory())
            ->withServiceAccount($credentials)
            ->createMessaging();
    }
}
