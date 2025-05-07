<?php

namespace App\Repository;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatRepository
{
    /**
     * Get the userId based chats
     */
    public function index(Request $request, $userId)
    {
       return Chat::where(function($query) use ($request, $userId) {
            $query->where('sender_id', $request->user()->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($request, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $request->user()->id);
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