<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Public chat routes
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    
    // Private chat routes
    Route::get('/chats/{userId}', [ChatController::class, 'getMessages']);
    Route::post('/chats', [ChatController::class, 'sendMessage']);
    
    // User routes
    Route::get('/users', [UserController::class, 'index']);
});