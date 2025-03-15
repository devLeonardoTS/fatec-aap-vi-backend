<?php

use App\Http\Controllers\MessagesController;
use Illuminate\Support\Facades\Route;

Route::prefix('messages')->name('messages.')->group(function () {
  Route::get('/', [MessagesController::class, 'index'])->name('index');

  Route::get('/actions', [MessagesController::class, 'actions'])->name('actions');

  Route::get('/{message}', [MessagesController::class, 'show'])->name('show');

  Route::post('/', [MessagesController::class, 'store'])->name('store');

  Route::patch('/{message}', [MessagesController::class, 'patch'])->name('patch');

  Route::delete('/{message}', [MessagesController::class, 'destroy'])->name('destroy');
});