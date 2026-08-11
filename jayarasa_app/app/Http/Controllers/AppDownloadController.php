<?php

namespace App\Http\Controllers;

use App\Models\Settings;

class AppDownloadController extends Controller
{
    /**
     * Public, unauthenticated download of the currently published Android APK.
     */
    public function download() {
        $data = $this->currentApkData();

        $path = !empty($data['filename']) ? storage_path('app/public/' . $data['filename']) : null;

        if (!$path || !file_exists($path)) {
            abort(404, 'Aplikasi Android belum tersedia.');
        }

        $downloadName = 'JayarasaPOS' . (!empty($data['version']) ? '-v' . $data['version'] : '') . '.apk';

        return response()->download($path, $downloadName, [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }

    /**
     * Public, unauthenticated version check for the Android app's in-app update prompt.
     * version_code is the source of truth for "is this newer" comparisons — version_name is
     * just the human-readable label shown in the update dialog.
     */
    public function version() {
        $data = $this->currentApkData();

        return response()->json([
            'version_code' => !empty($data['version_code']) ? (int) $data['version_code'] : 0,
            'version_name' => $data['version'] ?? null,
            'download_url' => !empty($data['filename']) ? route('app.download') : null,
        ]);
    }

    private function currentApkData(): array {
        $setting = Settings::where('jenis', 'app_apk')->first();
        return $setting && $setting->nilai ? (@unserialize($setting->nilai) ?: []) : [];
    }
}
