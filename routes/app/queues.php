<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueuesController;

Route::prefix('queues')->name('queues.')->group(function () {

  Route::get('/', [QueuesController::class, 'openCloseDevice'])->name('openCloseDevice');

  Route::post('/water_flow', [QueuesController::class, 'captureWaterFlowInfo'])->name('captureWaterFlowInfo');

  Route::post('/', [QueuesController::class, 'dispatchDeviceCommand'])->name('dispatchDeviceCommand');

});