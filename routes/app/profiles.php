<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilesController;
use Illuminate\Support\Facades\Route;

Route::prefix('profiles')->name('profiles.')->group(function () {

  Route::get('/', [ProfilesController::class, 'index'])->name('index');

  Route::post('/', [ProfilesController::class, 'store'])->name('store');

});