<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')
        ->where('id', '!=', auth()->id())
        ->with('chats')
        ->withMax('chats', 'created_at') 
        ->orderByDesc(column: 'chats_max_created_at')
        ->get();
        
        return UserResource::collection($users);
    }
} 