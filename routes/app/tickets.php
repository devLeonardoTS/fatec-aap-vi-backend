<?php

use App\Http\Controllers\TicketsController;
use Illuminate\Support\Facades\Route;

Route::prefix('tickets')->name('tickets.')
  ->middleware('auth:sanctum')
  ->group(function () {
    Route::get('/', [TicketsController::class, 'index'])->name('index');

    Route::get('/actions', [TicketsController::class, 'actions'])->name('actions');

    Route::get('/{ticket}', [TicketsController::class, 'show'])->name('show');

    Route::get('/{ticket}/comments', [TicketsController::class, 'showComments'])->name('showComments');

    Route::post('/', [TicketsController::class, 'store'])->name('store');

    Route::post('/{ticket}/comments', [TicketsController::class, 'storeComment'])->name('storeComment');

    Route::patch('/{ticket}', [TicketsController::class, 'patch'])->name('patch');

    Route::delete('/{ticket}', [TicketsController::class, 'destroy'])->name('destroy');
  });