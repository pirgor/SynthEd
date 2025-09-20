<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TtsSetting;

class SpeechController extends Controller
{
    public function index()
    {
        // Show the blade file
        return view('elevenlabs.sample');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
        ]);

        // ✅ Load user settings (with defaults if none exist)
        $userSettings = \App\Models\TtsSetting::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'voice_id' => 'onwK4e9ZLuTAKqWW03F9', // default male
                'speed' => 1.0,
                'stability' => 0.75,
                'similarity_boost' => 0.75,
            ]
        );

        $apiKey = env('ELEVENLABS_API_KEY');

        $response = Http::withHeaders([
            'xi-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post("https://api.elevenlabs.io/v1/text-to-speech/{$userSettings->voice_id}", [
            'text' => $request->input('text'),
            'model_id' => 'eleven_multilingual_v2',
            'output_format' => 'mp3_44100_128',
            'voice_settings' => [
                'stability' => (float) $userSettings->stability,
                'similarity_boost' => (float) $userSettings->similarity_boost,
                'speed' => (float) $userSettings->speed,
            ]
        ]);

        if (!$response->successful()) {
            Log::error('ElevenLabs API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return response()->json(['error' => 'Failed to generate audio'], 500);
        }

        return response($response->body(), 200)
            ->header('Content-Type', 'audio/mpeg')
            ->header('Content-Disposition', 'inline; filename="speech.mp3"');
    }
}
