<?php

use App\Http\Controllers\CommandsController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::prefix('commands')->name('commands.')
  ->middleware('auth:sanctum')
  ->group(function () {

    Route::post('/{command}', [CommandsController::class, 'executeCommand'])->name('executeCommand');

  });