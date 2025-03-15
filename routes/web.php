<?php

use App\Http\Controllers\ExampleController;
use App\Http\Controllers\MessageController;
use App\Middlewares\JsonOnlyForApiRoutes;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', function () {
  return response()->json('Hello World!');
});
Route::get('/messages',[MessageController::class,'index'])->name('messages.index');

Route::get('/messages/create',[MessageController::class,'create']) ->name('messages.create');

Route::post('/messages',[MessageController::class,'store']) ->name('messages.store');

Route::get('/messages/{message}',[MessageController::class,'show']) ->name('messages.show');

Route::get('/messages/{message}/edit',[MessageController::class,'edit']) ->name('messages.edit');

Route::put('/messages/{message}',[MessageController::class,'update']) ->name('messages.update');

Route::delete('/messages/{message}',[MessageController::class,'destroy']) ->name('messages.destroy'); 

// Handles "auth:sanctum" redirect when user is not authenticated.
Route::get('/login', function () {
  return response()->json(
    ['message' => 'Unauthenticated.'],
    Response::HTTP_UNAUTHORIZED
  );
});