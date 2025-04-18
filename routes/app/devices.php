<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::prefix('devices')->name('devices.')
  ->middleware('auth:sanctum')
  ->group(function () {

    Route::get('/analytics', [DeviceController::class, 'getAnalytics'])->name('getAnalytics');

    Route::get('/actions', [DeviceController::class, 'getActions'])->name('getActions');

    Route::get('/{device:token}', [DeviceController::class, 'showDevice'])->name('showDevice');

    Route::get('/{device:token}/analytics', [DeviceController::class, 'showDeviceAnalytics'])->name('showDeviceAnalytics');

    Route::get('/{device:token}/commands', [DeviceController::class, 'showDeviceCommands'])->name('showDeviceCommands');

    Route::get('/{device:token}/metrics', [DeviceController::class, 'showDeviceMetrics'])->name('showDeviceMetrics');

    Route::get('/', [DeviceController::class, 'getDevices'])->name('getDevices');

    Route::post('/{device:token}/commands', [DeviceController::class, 'storeDeviceCommand'])->name('storeDeviceCommand');

    Route::post('/{device:token}/metrics', [DeviceController::class, 'storeDeviceMetrics'])->name('storeDeviceMetrics');

    Route::post('/', [DeviceController::class, 'storeDevice'])->name('storeDevice');

    Route::patch('/{device:token}/commands/{command}', [DeviceController::class, 'patchDeviceCommand'])->name('patchDeviceCommand');

    Route::patch('/{device:token}', [DeviceController::class, 'patchDevice'])->name('patchDevice');

    Route::delete('/{device:token}/commands/{command}', [DeviceController::class, 'destroyDeviceCommand'])->name('destroyDeviceCommand');

    Route::delete('/{device:token}/commands/', [DeviceController::class, 'destroyAllDeviceCommands'])->name('destroyAllDeviceCommands');

  });