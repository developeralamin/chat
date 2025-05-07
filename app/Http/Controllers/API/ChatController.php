<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Events\PrivateMessageSent;
use App\Http\Requests\ChatRequest;
use App\Http\Resources\ChatResource;
use App\Repository\ChatRepository;

class ChatController extends Controller
{

    private ChatRepository $chat;

    public function __construct(ChatRepository $chat)
    {
        $this->chat = $chat;
    }

    public function getMessages($userId)
    {
        $messages = $this->chat->index( $userId);

        return response()->json(ChatResource::collection($messages));
    }

    public function sendMessage(ChatRequest $request)
    {
        try {
            $data = $request->only(['message','receiver_id']);
            $chat = $this->chat->store($data);

            $chat->load(['sender', 'receiver']);
            
            broadcast(new PrivateMessageSent($chat))->toOthers();
            
            return response()->json(new ChatResource($chat));
            
        } catch (\Exception $e) {
            return $this->fail('Failed to send message');
        }
    }
} 