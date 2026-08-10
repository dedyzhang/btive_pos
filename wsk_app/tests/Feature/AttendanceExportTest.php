<?php

namespace Tests\Feature;

use App\Exports\AttendanceMonthlyExport;
use App\Models\Settings;
use App\Models\User;
use App\Models\Attendances;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class AttendanceExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'http://localhost']);
    }

    public function test_admin_can_export_attendance_excel()
    {
        Excel::fake();

        // 1. Create an Admin user
        $admin = User::create([
            'name' => 'Admin Test',
            'role' => 'admin',
            'username' => 'admintest',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create some employees
        $employee = User::create([
            'name' => 'Employee Test',
            'role' => 'kasir',
            'username' => 'employeetest',
            'password' => Hash::make('password123'),
        ]);

        // 3. Create Settings for late time
        Settings::create([
            'jenis' => 'attendance_late_time',
            'nilai' => '08:00',
        ]);

        // 4. Create an attendance record
        Attendances::create([
            'user_id' => $employee->uuid,
            'tanggal' => date('Y-m-d'),
            'clock_in' => '07:45:00',
            'clock_out' => '17:00:00',
        ]);

        $this->withoutExceptionHandling();

        // 5. Act as Admin and hit the export route
        $response = $this->actingAs($admin)
            ->get('/attendance/export?' . http_build_query([
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d'),
            ]));

        $response->assertStatus(200);

        // 6. Assert excel downloaded
        Excel::assertDownloaded("Rekap_Kehadiran_Karyawan_" . date('Y-m-01') . "_to_" . date('Y-m-d') . ".xlsx");
    }

    public function test_non_admin_cannot_export_attendance_excel()
    {
        // 1. Create a non-admin user
        $nonAdmin = User::create([
            'name' => 'Cashier Test',
            'role' => 'kasir',
            'username' => 'cashiertest',
            'password' => Hash::make('password123'),
        ]);

        // 2. Act as non-admin and hit the export route
        $response = $this->actingAs($nonAdmin)
            ->get('/attendance/export?' . http_build_query([
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d'),
            ]));

        $response->assertStatus(403);
    }
}
