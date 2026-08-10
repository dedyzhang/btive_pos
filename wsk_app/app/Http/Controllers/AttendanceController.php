<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\User;
use App\Models\Settings;
use App\Exports\AttendanceMonthlyExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Check the authenticated user's attendance status for today.
     */
    public function checkToday()
    {
        $user = Auth::user();
        $today = date('Y-m-d');
        
        $attendance = Attendances::where('user_id', $user->uuid)
            ->where('tanggal', $today)
            ->first();

        $status = 'belum_absen';
        if ($attendance) {
            if (is_null($attendance->clock_out)) {
                $status = 'sudah_clock_in';
            } else {
                $status = 'sudah_clock_out';
            }
        }

        // Resolve profile picture using Vite asset helper or normal asset helper
        $profilePicture = $user->profile_picture 
            ? (str_starts_with($user->profile_picture, 'resources/') 
                ? Vite::asset($user->profile_picture) 
                : (str_starts_with($user->profile_picture, 'http') ? $user->profile_picture : asset($user->profile_picture)))
            : Vite::asset('resources/img/profile/boy_1.png');

        // Resolve attendance photos
        $fotoInUrl = null;
        $fotoOutUrl = null;
        if ($attendance) {
            if ($attendance->foto_in) {
                $fotoInUrl = asset('storage/attendance/' . $attendance->foto_in);
            }
            if ($attendance->foto_out) {
                $fotoOutUrl = asset('storage/attendance/' . $attendance->foto_out);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'attendance' => $attendance,
            'foto_in_url' => $fotoInUrl,
            'foto_out_url' => $fotoOutUrl,
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
                'profile_picture' => $profilePicture
            ]
        ]);
    }

    /**
     * Process employee Clock In.
     */
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $today = date('Y-m-d');

        // Validate photo is present
        $request->validate([
            'foto' => 'required'
        ], [
            'foto.required' => 'Foto bukti kehadiran wajib diunggah.'
        ]);

        // Check if attendance already exists today
        $existing = Attendances::where('user_id', $user->uuid)
            ->where('tanggal', $today)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan Clock In hari ini.'
            ], 422);
        }

        try {
            // Save and compress photo
            $filename = $this->saveAndCompressPhoto($request->foto);

            $attendance = Attendances::create([
                'user_id' => $user->uuid,
                'tanggal' => $today,
                'clock_in' => date('H:i:s'),
                'foto_in' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Clock In pagi ini dengan bukti foto. Semangat bekerja!',
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses foto absensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process employee Clock Out.
     */
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $today = date('Y-m-d');

        // Validate photo is present
        $request->validate([
            'foto' => 'required'
        ], [
            'foto.required' => 'Foto bukti pulang wajib diunggah.'
        ]);

        $attendance = Attendances::where('user_id', $user->uuid)
            ->where('tanggal', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan Clock In hari ini.'
            ], 422);
        }

        if (!is_null($attendance->clock_out)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan Clock Out hari ini.'
            ], 422);
        }

        try {
            // Save and compress photo
            $filename = $this->saveAndCompressPhoto($request->foto);

            $attendance->update([
                'clock_out' => date('H:i:s'),
                'foto_out' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Clock Out sore ini dengan bukti foto. Selamat istirahat!',
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses foto absensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to save and compress Base64 photo to storage.
     */
    private function saveAndCompressPhoto($base64Data)
    {
        // Strip header if exists
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        }

        $data = base64_decode($base64Data);
        if ($data === false) {
            throw new \Exception('Dekode base64 gagal.');
        }

        // Generate filename
        $filename = Str::random(40) . '.jpg';
        $dir = storage_path('app/public/attendance');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir . '/' . $filename;

        // Server-side compression using GD
        $img = @imagecreatefromstring($data);
        if ($img !== false) {
            // Force save as JPEG with 60% quality to compress it heavily
            imagejpeg($img, $path, 60);
            imagedestroy($img);
        } else {
            // Fallback: save raw if GD fails
            file_put_contents($path, $data);
        }

        return $filename;
    }

    /**
     * Display a listing of employee attendance records (Admin Recap).
     */
    public function recapIndex(Request $request)
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-d'));
        $userId = $request->query('user_id');

        $query = Attendances::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->orderBy('tanggal', 'desc')->get();
        $users = User::all();
        $lateTime = Settings::where('jenis', 'attendance_late_time')->first()->nilai ?? '08:00';

        return view('attendance.recap', compact('attendances', 'users', 'lateTime', 'startDate', 'endDate', 'userId'));
    }

    /**
     * Export employee attendance records to a monthly pivot matrix table using Laravel Excel.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-d'));
        $userId = $request->query('user_id');

        // Fetch users
        $userQuery = User::query();
        if ($userId) {
            $userQuery->where('uuid', $userId);
        }
        $users = $userQuery->orderBy('name', 'asc')->get();

        // Fetch attendances
        $attendanceQuery = Attendances::whereBetween('tanggal', [$startDate, $endDate]);
        if ($userId) {
            $attendanceQuery->where('user_id', $userId);
        }
        $attendances = $attendanceQuery->get();

        // Late Time setting
        $lateTime = Settings::where('jenis', 'attendance_late_time')->first()->nilai ?? '08:00';

        // Generate date range
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $end->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end);

        $dates = [];
        foreach ($dateRange as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        // Map attendances to [user_id][tanggal] = attendance
        $attendanceMap = [];
        foreach ($attendances as $att) {
            $attendanceMap[$att->user_id][$att->tanggal] = $att;
        }

        $filename = "Rekap_Kehadiran_Karyawan_" . $startDate . "_to_" . $endDate . ".xlsx";

        return Excel::download(
            new AttendanceMonthlyExport($users, $dates, $attendanceMap, $lateTime, $startDate, $endDate),
            $filename
        );
    }
}
