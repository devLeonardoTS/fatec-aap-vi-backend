<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('devices')->name('devices.')->group(function () {

  Route::get('/', [DeviceController::class, 'index'])->name('index');

  Route::get('/{device:token}', [DeviceController::class, 'show'])->name('show');

  Route::post('/', [DeviceController::class, 'store'])->name('store');

});