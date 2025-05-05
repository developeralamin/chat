<?php

namespace App\Http\Controllers\API;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function index()
    {
        return Message::with('user')->latest()->take(50)->get()->reverse()->values();
    }

    public function store(Request $request)
    {
        try {
            Log::info('Creating new message', ['user_id' => $request->user()->id, 'message' => $request->message]);
            
            $message = $request->user()->messages()->create([
                'message' => $request->message
            ]);

            // Load the user relationship before broadcasting
            $message->load('user');
            
            Log::info('Broadcasting message', ['message_id' => $message->id]);
            
            // Broadcast to everyone except the sender
            broadcast(new MessageSent($message))->toOthers();
            
            Log::info('Message broadcasted successfully');
            
            return $message;
        } catch (\Exception $e) {
            Log::error('Error in message store:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
