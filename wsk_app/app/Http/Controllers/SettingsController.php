<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Tables;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index() : View {
        $tables = Tables::orderBy('sort','asc')->get();
        $settings = Settings::all();
        $categories = \App\Models\Categories::orderBy('sort','asc')->get();
        return view('settings.index',compact('tables','settings','categories'));
    }
    /**
     * Tables - create Tables
     */
    public function tableCreate(Request $request) {
        $request->validate([
            'table_name' => 'required',
            'table_color' => 'required',
        ]);
        $table_count = Tables::count() + 1;
        Tables::create([
            'name' => $request->table_name,
            'color' => $request->table_color,
            'sort' => $table_count,
            'status' => 'empty'
        ]);
        return redirect()->back()->with('success_table','Successfully Add Table');
    }
    /**
     * Tables - Sort Tables
     */
    public function tableSort(Request $request) {
        $tables = $request->urutan;

        Tables::upsert($tables, 'uuid', ['sort','name','color']);

        return response()->json(['success' => true,'message' => 'Successfully Sorted the table']);
    }
    /**
     * Tables - Delete Tables
     */
    public function tableDelete(String $uuid) {
        $table = Tables::findOrFail($uuid)->delete();

        return response()->json(['success' => true, "message" => 'Successfully Deleted Table']);
    }
    public function paymentTaxUpdate(Request $request) {
        $request->validate([
            'tax' => 'required|numeric|min:0'
        ]);

        Settings::updateOrCreate(
            ['jenis' => 'payment_tax'],
            ['nilai' => $request->tax]
        );

        return redirect()->back()->with('success_tax','Successfully Updated Payment Tax');
    }
    public function restaurantUpdate(Request $request) {
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
        ]);
        // dd($request->all());
        $oldSetting = Settings::where('jenis','restaurant_logo')->first();
        $file = $request->file('picture');
        if ($request->is_changed == 1) {
            if ($oldSetting && $oldSetting->nilai != "") {
                $oldPath = storage_path('app/public/') . $oldSetting->nilai;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            if ($file == null) {
                Settings::updateOrCreate(
                    ['jenis' => 'restaurant_logo'],
                    ['nilai' => '']
                );
            } else {
                $filename = $file->hashName();
                $file->storeAs('', $filename, 'public');
                Settings::updateOrCreate(
                    ['jenis' => 'restaurant_logo'],
                    ['nilai' => $filename]
                );
            }
        } else {
            if ($file != null) {
                if ($oldSetting && $oldSetting->nilai != "") {
                    $oldPath = storage_path('app/public/') . $oldSetting->nilai;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $filename = $file->hashName();
                $file->storeAs('', $filename, 'public');
                Settings::updateOrCreate(
                    ['jenis' => 'restaurant_logo'],
                    ['nilai' => $filename]
                );
            }
        }
        $restaurant_setting = array(
            'name' => $request->restaurant_name,
            'location' => $request->restaurant_location,
            'accent_color' => $request->restaurant_accent_color ?? '#2b66ff'
        );

        Settings::updateOrCreate(
            ['jenis' => 'restaurant_settings'],
            ['nilai' => serialize($restaurant_setting)]
        );

        return redirect()->back()->with('success_restaurant','Successfully Updated Restaurant Data');
    }

    public function attendanceLateUpdate(Request $request) {
        $request->validate([
            'late_time' => 'required'
        ]);

        Settings::updateOrCreate(
            ['jenis' => 'attendance_late_time'],
            ['nilai' => $request->late_time]
        );

        return redirect()->back()->with('success_late_time', 'Jam Batas Keterlambatan Absensi Berhasil Diperbarui');
    }

    /**
     * Android App (APK) - Upload/Replace
     */
    public function apkUpload(Request $request) {
        $request->validate([
            'apk_file' => 'required|file|max:153600', // 150MB
            'version' => 'nullable|string|max:50',
        ]);

        $file = $request->file('apk_file');
        if (strtolower($file->getClientOriginalExtension()) !== 'apk') {
            return redirect()->back()->withErrors(['apk_file' => 'File harus berformat .apk'])->withInput();
        }

        $oldSetting = Settings::where('jenis', 'app_apk')->first();
        if ($oldSetting && $oldSetting->nilai) {
            $oldData = @unserialize($oldSetting->nilai) ?: [];
            if (!empty($oldData['filename'])) {
                $oldPath = storage_path('app/public/' . $oldData['filename']);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        $filename = $file->hashName();
        $file->storeAs('apk', $filename, 'public');

        Settings::updateOrCreate(
            ['jenis' => 'app_apk'],
            ['nilai' => serialize([
                'filename' => 'apk/' . $filename,
                'original_name' => $file->getClientOriginalName(),
                'version' => $request->version,
                'size' => $file->getSize(),
                'uploaded_at' => now()->toDateTimeString(),
            ])]
        );

        return redirect()->back()->with('success_apk', 'Aplikasi Android Berhasil Diunggah');
    }

    /**
     * Android App (APK) - Delete
     */
    public function apkDelete() {
        $setting = Settings::where('jenis', 'app_apk')->first();
        if ($setting && $setting->nilai) {
            $data = @unserialize($setting->nilai) ?: [];
            if (!empty($data['filename'])) {
                $path = storage_path('app/public/' . $data['filename']);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
            $setting->delete();
        }

        return redirect()->back()->with('success_apk', 'Aplikasi Android Berhasil Dihapus');
    }

    /**
     * Categories - Sort Categories
     */
    public function categorySort(Request $request) {
        $categories = $request->urutan;

        \App\Models\Categories::upsert($categories, 'uuid', ['sort','nama','icon','color']);

        return response()->json(['success' => true,'message' => 'Successfully Sorted the categories']);
    }
}
