<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendances;
use App\Models\SalaryAdjustment;
use App\Models\Settings;
use App\Models\CashFlowAccount;
use App\Models\CashFlowTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    /**
     * Display payroll list with monthly summary calculations.
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->query('month', date('Y-m'));
        
        $carbonMonth = Carbon::parse($selectedMonth . '-01');
        $startDate = $carbonMonth->copy()->startOfMonth()->toDateString();
        $endDate = $carbonMonth->copy()->endOfMonth()->toDateString();

        $users = User::orderBy('name', 'asc')->get();

        // Fetch payouts for this month
        $payouts = CashFlowTransaction::with('account')
            ->where('reference', 'like', 'PAYROLL:%:' . $selectedMonth)
            ->get()
            ->keyBy(function($trx) {
                $parts = explode(':', $trx->reference);
                return $parts[1] ?? '';
            });

        // Map data per user
        $payrollData = [];
        foreach ($users as $user) {
            // Count actual successful attendances (days they clocked in)
            $totalDaysWorked = Attendances::where('user_id', $user->uuid)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->whereNotNull('clock_in')
                ->count();

            // Calculate base salary
            $dailySalary = $user->daily_salary ?? 0;
            $baseSalaryTotal = $totalDaysWorked * $dailySalary;

            // Get adjustments
            $adjustments = SalaryAdjustment::where('user_id', $user->uuid)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $totalBonuses = $adjustments->where('type', 'bonus')->sum('amount');
            $totalDeductions = $adjustments->where('type', 'deduction')->sum('amount');
            
            $netSalary = $baseSalaryTotal + $totalBonuses - $totalDeductions;
            $payout = $payouts->get($user->uuid);

            $payrollData[] = [
                'user' => $user,
                'total_days_worked' => $totalDaysWorked,
                'base_salary_total' => $baseSalaryTotal,
                'total_bonuses' => $totalBonuses,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'adjustments' => $adjustments,
                'payout' => $payout
            ];
        }

        $accounts = CashFlowAccount::all();

        return view('payroll.index', compact('payrollData', 'selectedMonth', 'accounts'));
    }

    /**
     * Update user's daily salary.
     */
    public function updateDailySalary(Request $request, String $uuid)
    {
        $request->validate([
            'daily_salary' => 'required|integer|min:0',
        ]);

        $user = User::findOrFail($uuid);
        $user->update([
            'daily_salary' => $request->daily_salary
        ]);

        return redirect()->back()->with('success_payroll', 'Gaji harian ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Store salary adjustment (bonus/deduction).
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,uuid',
            'tanggal' => 'required|date',
            'type' => 'required|in:bonus,deduction',
            'amount' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        SalaryAdjustment::create([
            'user_id' => $request->user_id,
            'tanggal' => $request->tanggal,
            'type' => $request->type,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success_payroll', 'Penyesuaian gaji berhasil disimpan.');
    }

    /**
     * Delete salary adjustment.
     */
    public function destroyAdjustment(String $uuid)
    {
        $adjustment = SalaryAdjustment::findOrFail($uuid);
        $adjustment->delete();

        return redirect()->back()->with('success_payroll', 'Penyesuaian gaji berhasil dihapus.');
    }

    /**
     * View print slip page.
     */
    public function printPayslip(Request $request, String $uuid)
    {
        $user = User::findOrFail($uuid);
        $selectedMonth = $request->query('month', date('Y-m'));

        $carbonMonth = Carbon::parse($selectedMonth . '-01');
        $startDate = $carbonMonth->copy()->startOfMonth()->toDateString();
        $endDate = $carbonMonth->copy()->endOfMonth()->toDateString();

        // Get detailed attendance list for this month
        $attendances = Attendances::where('user_id', $user->uuid)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Late Time setting
        $lateTime = Settings::where('jenis', 'attendance_late_time')->first()->nilai ?? '08:00';

        // Get adjustments
        $adjustments = SalaryAdjustment::where('user_id', $user->uuid)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalDaysWorked = $attendances->whereNotNull('clock_in')->count();
        $dailySalary = $user->daily_salary ?? 0;
        $baseSalaryTotal = $totalDaysWorked * $dailySalary;

        $totalBonuses = $adjustments->where('type', 'bonus')->sum('amount');
        $totalDeductions = $adjustments->where('type', 'deduction')->sum('amount');
        $netSalary = $baseSalaryTotal + $totalBonuses - $totalDeductions;

        // Retrieve logo
        $settingrestaurantlogo = Settings::where('jenis', 'restaurant_logo')->first();
        $settingrestaurant = Settings::where('jenis', 'restaurant_settings')->first();
        
        $settingResArray = [];
        if($settingrestaurant && $settingrestaurant->nilai) {
            $settingResArray = @unserialize($settingrestaurant->nilai);
            if ($settingResArray === false) {
                $settingResArray = @unserialize(stripslashes($settingrestaurant->nilai)) ?: [];
            }
        }
        $resName = $settingResArray['name'] ?? 'Restaurant';
        $resLocation = $settingResArray['address'] ?? '';
        $imgPath = $settingrestaurantlogo && $settingrestaurantlogo->nilai ? asset('storage/' . $settingrestaurantlogo->nilai) : null;

         return view('payroll.print', compact(
            'user',
            'attendances',
            'lateTime',
            'adjustments',
            'totalDaysWorked',
            'dailySalary',
            'baseSalaryTotal',
            'totalBonuses',
            'totalDeductions',
            'netSalary',
            'selectedMonth',
            'resName',
            'resLocation',
            'imgPath'
        ));
    }

    /**
     * Get attendance calendar data for a user in a specific month.
     */
    public function getCalendarData(Request $request, String $uuid)
    {
        $user = User::findOrFail($uuid);
        $selectedMonth = $request->query('month', date('Y-m'));

        $carbonMonth = Carbon::parse($selectedMonth . '-01');
        $startDate = $carbonMonth->copy()->startOfMonth()->toDateString();
        $endDate = $carbonMonth->copy()->endOfMonth()->toDateString();

        // Get all attendance records for this month
        $attendances = Attendances::where('user_id', $user->uuid)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy('tanggal');

        $lateTime = Settings::where('jenis', 'attendance_late_time')->first()->nilai ?? '08:00';

        // Generate calendar array
        $daysInMonth = $carbonMonth->daysInMonth;
        $calendarDays = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = $carbonMonth->copy()->day($day)->toDateString();
            $attendance = $attendances->get($dateStr);
            
            $status = 'absent';
            $clockIn = null;
            $clockOut = null;
            $isLate = false;

            if ($attendance) {
                $clockIn = substr($attendance->clock_in, 0, 5);
                $clockOut = $attendance->clock_out ? substr($attendance->clock_out, 0, 5) : null;
                $isLate = $attendance->clock_in > $lateTime;
                $status = $isLate ? 'late' : 'present';
            } else {
                // If the day is in the future, it's 'future' instead of 'absent'
                if ($dateStr > date('Y-m-d')) {
                    $status = 'future';
                }
            }

            $calendarDays[] = [
                'day' => $day,
                'date' => $dateStr,
                'status' => $status,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'is_late' => $isLate,
            ];
        }

        return response()->json([
            'success' => true,
            'employee_name' => $user->name,
            'month_name' => $carbonMonth->translatedFormat('F Y'),
            'start_of_week' => $carbonMonth->dayOfWeek, // 0 (Sunday) to 6 (Saturday)
            'days' => $calendarDays,
        ]);
    }

    /**
     * Store salary payment as a Cash Flow Transaction.
     */
    public function storePayout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,uuid',
            'month' => 'required|date_format:Y-m',
            'account_id' => 'required|exists:cash_flow_accounts,uuid',
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->user_id);
        $reference = "PAYROLL:" . $user->uuid . ":" . $request->month;

        // Check if already paid
        $existingPayout = CashFlowTransaction::where('reference', $reference)->first();
        if ($existingPayout) {
            return redirect()->back()->withErrors(['payroll_error' => 'Gaji karyawan ini untuk periode tersebut sudah dibayar.']);
        }

        // Find or create 'Gaji Karyawan' expense category
        $category = \App\Models\CashFlowCategory::firstOrCreate(
            ['name' => 'Gaji Karyawan', 'type' => 'expense']
        );

        // Create transaction
        CashFlowTransaction::create([
            'account_id' => $request->account_id,
            'type' => 'expense',
            'amount' => $request->amount,
            'transaction_date' => date('Y-m-d'),
            'description' => $request->description,
            'reference' => $reference,
            'category_id' => $category->uuid,
        ]);

        return redirect()->back()->with('success_payroll', 'Pembayaran gaji ' . $user->name . ' berhasil dibukukan ke Arus Kas.');
    }

    /**
     * Cancel salary payment (delete Cash Flow Transaction).
     */
    public function destroyPayout(String $uuid)
    {
        $transaction = CashFlowTransaction::findOrFail($uuid);
        
        // Ensure it is a payroll transaction
        if (!str_starts_with($transaction->reference, 'PAYROLL:')) {
            abort(403, 'Hanya transaksi pembayaran gaji yang dapat dibatalkan melalui menu ini.');
        }

        $transaction->delete();

        return redirect()->back()->with('success_payroll', 'Pembayaran gaji berhasil dibatalkan dan dihapus dari Arus Kas.');
    }
}
