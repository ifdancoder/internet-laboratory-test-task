<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::controller(UserController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('authenticate', 'authenticate');
    Route::get('users', 'getUsers');
    Route::get('users/{id}', 'getUser');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('users/{id}', 'deleteUser');
        Route::put('users/{id}', 'updateUser');
    });
});