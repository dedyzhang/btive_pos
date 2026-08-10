<?php

namespace App\Http\Controllers;

use App\Models\Settings;

class AppDownloadController extends Controller
{
    /**
     * Public, unauthenticated download of the currently published Android APK.
     */
    public function download() {
        $setting = Settings::where('jenis', 'app_apk')->first();
        $data = $setting && $setting->nilai ? (@unserialize($setting->nilai) ?: []) : [];

        $path = !empty($data['filename']) ? storage_path('app/public/' . $data['filename']) : null;

        if (!$path || !file_exists($path)) {
            abort(404, 'Aplikasi Android belum tersedia.');
        }

        $downloadName = 'JayarasaPOS' . (!empty($data['version']) ? '-v' . $data['version'] : '') . '.apk';

        return response()->download($path, $downloadName, [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
}
