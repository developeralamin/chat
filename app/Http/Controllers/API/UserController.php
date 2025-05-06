<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')
            ->where('id', '!=', auth()->id())
            ->get();

        return response()->json($users);
    }
} 