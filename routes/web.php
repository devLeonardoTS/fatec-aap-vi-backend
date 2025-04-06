<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get('/', function () {
  return response()->json('Hello World!');
});

// Handles "auth:sanctum" redirect when user is not authenticated.
Route::get('/login', function () {
  return response()->json(
    ['message' => 'Unauthenticated.'],
    Response::HTTP_UNAUTHORIZED
  );
});