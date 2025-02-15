<?php

use App\Http\Controllers\AuthController;



Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', [AuthController::class, 'user'])
  ->middleware('auth:sanctum');

Route::post('/logout', [AuthController::class, 'logout'])
  ->middleware('auth:sanctum');