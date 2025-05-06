<?php

namespace App\Http\Controllers\API;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\PrivateMessageSent;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function getMessages(Request $request, $userId)
    {
        $messages = Chat::where(function($query) use ($request, $userId) {
            $query->where('sender_id', $request->user()->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($request, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $request->user()->id);
        })
        ->with(['sender', 'receiver'])
        ->latest()
        ->take(50)
        ->get()
        ->reverse()
        ->values();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        try {
            $chat = Chat::create([
                'sender_id' => $request->user()->id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message
            ]);

            $chat->load(['sender', 'receiver']);
            
            broadcast(new PrivateMessageSent($chat))->toOthers();
            
            return response()->json($chat);
        } catch (\Exception $e) {
            Log::error('Error in chat message store:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }
} 