<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        Log::info('MessageSent event constructed', [
            'message_id' => $message->id,
            'user_id' => $message->user_id,
            'message' => $message->message
        ]);
        $this->message = $message;
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting on channel: chat');
        return new Channel('chat');
    }

    public function broadcastWith()
    {
        $broadcastData = [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name
                ],
                'created_at' => $this->message->created_at
            ]
        ];

        Log::info('Broadcasting message data', $broadcastData);
        return $broadcastData;
    }
}
