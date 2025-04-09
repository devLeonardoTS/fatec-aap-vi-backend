<?php

use App\Http\Controllers\AuthController;

Route::prefix('auth')->name('auth.')->group(function () {

  Route::post('/login', [AuthController::class, 'login'])
    ->name('login');

  Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [AuthController::class, 'user'])
      ->name('user');

    Route::post('/logout', [AuthController::class, 'logout'])
      ->name('logout');
  });

});