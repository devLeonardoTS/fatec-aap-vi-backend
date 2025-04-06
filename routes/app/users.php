<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->group(function () {

  Route::get('/', [UsersController::class, 'index'])->name('index');

  Route::post('/', [UsersController::class, 'store'])->name('store');

});