<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\Role;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
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
                return;
            }

            $totalFormatted = 'Rp ' . number_format((float) $transaction->total, 0, ',', '.');

            $message = CloudMessage::new()
                ->withNotification(FcmNotification::create(
                    'Pembayaran Berhasil',
                    "Order {$transaction->invoice_number} - {$totalFormatted} telah dibayar"
                ))
                ->withData([
                    'type' => 'payment_success',
                    'transaction_uuid' => $transaction->uuid,
                    'invoice_number' => (string) $transaction->invoice_number,
                    'total' => (string) $transaction->total,
                    'click_action' => route('transaction.print.payment', $transaction->uuid),
                ]);

            $this->sendToTokens($tokens, $message);
        } catch (Throwable $e) {
            Log::error('FCM notifyPaymentSuccess failed: ' . $e->getMessage());
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
