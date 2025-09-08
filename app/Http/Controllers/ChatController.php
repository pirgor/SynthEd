<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            // Logged-in users → fetch from DB
            $chatHistory = Message::where('user_id', Auth::id())
                ->orderBy('created_at')
                ->get();
        } else {
            // Guests → load from session
            $chatHistory = session()->get('chatHistory', []);
        }

        return view('chatbot.chatbot', compact('chatHistory'));
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->message;

        if (Auth::check()) {
            // Logged-in → get history from DB
            $chatHistory = Message::where('user_id', Auth::id())
                ->orderBy('created_at')
                ->get();
        } else {
            // Guest → get history from session
            $chatHistory = session()->get('chatHistory', []);
        }

        // Format history for Gemini
        $contents = [];
        foreach ($chatHistory as $msg) {
            $userMsg = is_array($msg) ? $msg['user_message'] : $msg->user_message;
            $botMsg  = is_array($msg) ? $msg['bot_reply'] : $msg->bot_reply;

            if ($userMsg) {
                $contents[] = ['role' => 'user', 'parts' => [['text' => $userMsg]]];
            }
            if ($botMsg) {
                $contents[] = ['role' => 'model', 'parts' => [['text' => $botMsg]]];
            }
        }

        // Add current user message
        $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        // Call Gemini
        $botReply = $this->getGeminiReply($contents);

        if (Auth::check()) {
            // Save to DB
            $message = Message::create([
                'user_id' => Auth::id(),
                'user_message' => $userMessage,
                'bot_reply' => $botReply,
            ]);
        } else {
            // Save to session
            $message = [
                'user_message' => $userMessage,
                'bot_reply' => $botReply,
            ];
            $history = session()->get('chatHistory', []);
            $history[] = $message;
            session()->put('chatHistory', $history);
        }

        if ($request->ajax()) {
            return response()->json([
                'user' => $userMessage,
                'reply' => $botReply,
            ]);
        }

        return redirect()->route('chat.show');
    }

    private function getGeminiReply($contents)
    {
        $apiKey = env('GEMINI_API_KEY');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
            'contents' => $contents
        ]);

        if ($response->successful()) {
            return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Sorry, no reply.';
        }

        return 'Error reaching Gemini.';
    }
}
