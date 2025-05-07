<?php

namespace App\Http\Resources;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authId = Auth::id();
        
        $lastMessage = Chat::where(function ($query) use ($authId) {
            $query->where('sender_id', $authId)
                ->where('receiver_id', $this->id);
        })->orWhere(function ($query) use ($authId) {
            $query->where('sender_id', $this->id)
                    ->where('receiver_id', $authId);
        })->latest('id')->first();

    
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'last_message' => $lastMessage ? $lastMessage->message :  'No conversation yet' ,
        ];
    }
}
