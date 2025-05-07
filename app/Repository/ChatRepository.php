<?php

namespace App\Repository;

use App\Models\Chat;

class ChatRepository
{
    /**
     * Get the userId based chats
     */
    public function index($userId)
    {
        $authUserId = auth()->id();

       return Chat::where(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $authUserId)
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $authUserId);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc') 
        ->get();
    }


    /**
     * Store message
     */
    public function store(array $data)
    {
        $data['sender_id'] = auth()->id();

        return Chat::create($data);
    }

}