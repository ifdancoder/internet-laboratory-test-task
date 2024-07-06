<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::controller(UserController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('authenticate', 'authenticate');
    Route::get('user', 'getUser');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('user', 'deleteUser');
        Route::put('user', 'updateUser');
    });
});