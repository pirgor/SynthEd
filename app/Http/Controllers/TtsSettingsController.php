<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TtsSetting;
use Illuminate\Support\Facades\Auth;

class TtsSettingsController extends Controller
{
    public function edit()
    {
        $settings = TtsSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'voice_id' => 'onwK4e9ZLuTAKqWW03F9',
                'speed' => 1.0,
            ]
        );

        return view('elevenlabs.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'voice_id' => 'required|string',
            'speed' => 'required|numeric|min:0.7|max:1.2',
        ]);

        $settings = TtsSetting::where('user_id', Auth::id())->firstOrFail();
        $settings->update([
            'voice_id' => $request->voice_id,
            'speed' => $request->speed,
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}
