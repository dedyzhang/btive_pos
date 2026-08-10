<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Settings;
use App\Models\Attendances;
use App\Models\SalaryAdjustment;
use App\Models\CashFlowAccount;
use App\Models\CashFlowTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $employee;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Admin
        $this->admin = User::create([
            'name' => 'Dedy Admin',
            'username' => 'dedy',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        // 2. Create Employee
        $this->employee = User::create([
            'name' => 'Staff Kasir',
            'username' => 'kasir1',
            'email' => 'kasir1@test.com',
            'password' => bcrypt('password123'),
            'role' => 'cashier',
            'daily_salary' => 100000
        ]);

        // 3. Create Late Time setting
        Settings::create([
            'jenis' => 'attendance_late_time',
            'nilai' => '08:00'
        ]);

        // 4. Create Restaurant setting
        Settings::create([
            'jenis' => 'restaurant_settings',
            'nilai' => serialize([
                'name' => 'Jaya Rasa',
                'address' => 'Jl. DI Panjaitan KM 9',
                'accent_color' => '#2b66ff'
            ])
        ]);
    }

    /**
     * Test admin can access payroll dashboard page.
     */
    public function test_admin_can_access_payroll_dashboard()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('payroll.index'));

        $response->assertStatus(200);
        $response->assertSee('PENGGAJIAN STAF');
        $response->assertSee('Staff Kasir');
        $response->assertSee('Rp 100.000');
    }

    /**
     * Test admin can update employee's daily salary.
     */
    public function test_admin_can_update_daily_salary()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('payroll.salary.update', $this->employee->uuid), [
                'daily_salary' => 125000
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success_payroll');

        $this->assertDatabaseHas('users', [
            'uuid' => $this->employee->uuid,
            'daily_salary' => 125000
        ]);
    }

    /**
     * Test admin can manage salary adjustments (bonus/deduction).
     */
    public function test_admin_can_store_and_delete_salary_adjustment()
    {
        // Add Adjustment (Bonus)
        $response = $this->actingAs($this->admin)
            ->post(route('payroll.adjustments.store'), [
                'user_id' => $this->employee->uuid,
                'tanggal' => '2026-06-30',
                'type' => 'bonus',
                'amount' => 50000,
                'notes' => 'Bonus Rajin'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success_payroll');

        $this->assertDatabaseHas('salary_adjustments', [
            'user_id' => $this->employee->uuid,
            'tanggal' => '2026-06-30',
            'type' => 'bonus',
            'amount' => 50000,
            'notes' => 'Bonus Rajin'
        ]);

        $adj = SalaryAdjustment::first();

        // Delete Adjustment
        $deleteResponse = $this->actingAs($this->admin)
            ->delete(route('payroll.adjustments.destroy', $adj->uuid));

        $deleteResponse->assertStatus(302);
        $deleteResponse->assertSessionHas('success_payroll');

        $this->assertDatabaseMissing('salary_adjustments', [
            'uuid' => $adj->uuid
        ]);
    }

    /**
     * Test admin can view payslip print page.
     */
    public function test_admin_can_view_payslip_print_page()
    {
        // Create an attendance record
        Attendances::create([
            'user_id' => $this->employee->uuid,
            'tanggal' => '2026-06-15',
            'clock_in' => '07:55:00',
            'clock_out' => '17:00:00'
        ]);

        // Create an adjustment record
        SalaryAdjustment::create([
            'user_id' => $this->employee->uuid,
            'tanggal' => '2026-06-20',
            'type' => 'deduction',
            'amount' => 20000,
            'notes' => 'Denda Terlambat'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('payroll.print', [
                'uuid' => $this->employee->uuid,
                'month' => '2026-06'
            ]));

        $response->assertStatus(200);
        $response->assertSee('SLIP GAJI STAF');
        $response->assertSee('Staff Kasir');
        $response->assertSee('Rp 100.000'); // daily rate
        $response->assertSee('Rp 20.000');  // deduction
        $response->assertSee('Denda Terlambat');
        $response->assertSee('Jaya Rasa');
    }

    /**
     * Test admin can retrieve calendar JSON data.
     */
    public function test_admin_can_retrieve_calendar_data()
    {
        // Create an attendance record
        Attendances::create([
            'user_id' => $this->employee->uuid,
            'tanggal' => '2026-06-15',
            'clock_in' => '07:55:00',
            'clock_out' => '17:00:00'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('payroll.calendar-data', [
                'uuid' => $this->employee->uuid,
                'month' => '2026-06'
            ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'employee_name' => 'Staff Kasir',
            'month_name' => 'Juni 2026',
            'start_of_week' => 1 // 1 June 2026 is Monday (1)
        ]);

        $this->assertEquals(30, count($response->json('days')));
    }

    /**
     * Test admin can pay employee salary (integrates with cash flow).
     */
    public function test_admin_can_pay_employee_salary()
    {
        // Create Cash Flow Account
        $account = CashFlowAccount::create([
            'name' => 'KAS TOKO UTAMA',
            'initial_balance' => 5000000
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('payroll.payout.store'), [
                'user_id' => $this->employee->uuid,
                'month' => '2026-06',
                'account_id' => $account->uuid,
                'amount' => 1500000,
                'description' => 'Pembayaran Gaji Staff Kasir - Juni 2026'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success_payroll');

        // Verify Cash Flow Transaction is created
        $this->assertDatabaseHas('cash_flow_transactions', [
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 1500000,
            'reference' => 'PAYROLL:' . $this->employee->uuid . ':2026-06',
            'description' => 'Pembayaran Gaji Staff Kasir - Juni 2026'
        ]);
    }

    /**
     * Test admin can cancel employee salary payment.
     */
    public function test_admin_can_cancel_salary_payment()
    {
        // Create Cash Flow Account
        $account = CashFlowAccount::create([
            'name' => 'KAS TOKO UTAMA',
            'initial_balance' => 5000000
        ]);

        // Create Payout Transaction
        $trx = CashFlowTransaction::create([
            'account_id' => $account->uuid,
            'type' => 'expense',
            'amount' => 1500000,
            'transaction_date' => '2026-06-30',
            'description' => 'Pembayaran Gaji Staff Kasir - Juni 2026',
            'reference' => 'PAYROLL:' . $this->employee->uuid . ':2026-06'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('payroll.payout.destroy', $trx->uuid));

        $response->assertStatus(302);
        $response->assertSessionHas('success_payroll');

        // Verify Cash Flow Transaction is deleted
        $this->assertDatabaseMissing('cash_flow_transactions', [
            'uuid' => $trx->uuid
        ]);
    }
}
