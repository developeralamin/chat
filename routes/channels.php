<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('chat', function ($user) {
//     return [
//         'id' => $user->id,
//         'name' => $user->name,
//         'email' => $user->email
//     ];
// });

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}); 