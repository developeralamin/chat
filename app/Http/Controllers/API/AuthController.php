<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        return $this->success($user);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->fail('Incorrect email address. Please use correct email');
        }

        if (Hash::check($request->password, $user->password)) {
            $token    = $user->createToken('authToken')->plainTextToken;
            $response = [
                'user'   => $user,
                'token'  => $token
            ];
            return $this->success($response);
        }
        return $this->fail('Invalid credentials.', 401);
    }
}
