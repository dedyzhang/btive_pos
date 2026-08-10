<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Register (or refresh) the FCM token for the logged-in admin's device.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:512',
        ]);

        DeviceToken::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            [
                'user_id' => auth()->id(),
                'platform' => 'android',
                'last_used_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Remove a token, e.g. on logout, so a shared/reset device stops receiving pushes.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string|max:512',
        ]);

        DeviceToken::where('fcm_token', $request->fcm_token)->delete();

        return response()->json(['success' => true]);
    }
}
