<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $response = Http::withToken(config('services.groq.api_key'))
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'Kamu adalah asisten AI yang ramah dan membantu, jawab dalam Bahasa Indonesia.'],
                    ['role' => 'user', 'content' => $request->message],
                ],
            ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Gagal menghubungi Groq API: ' . $response->body(),
            ], 500);
        }

        $reply = $response->json('choices.0.message.content');

        return response()->json(['reply' => $reply]);
    }
}
