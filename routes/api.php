<?php

Route::middleware('auth:sanctum')->get('/protected', function (Request $request) {
  return response()->json([
    'message' => 'This is a protected route.',
    // 'user' => $request->user(),
  ]);
})->name('protected');

Route::name('test.')->prefix('test')->group(function () {
  Route::get('/', function () {
    return response()->json(['message' => 'GET TEST SUCCESSFUL']);
  })->name('get');

  Route::post('/', function (Request $request) {
    return response()->json(['message' => 'POST TEST SUCCESSFUL']);
  })->name('post');
});

// Rotas de autenticação separada em um arquivo específico (recomendado)
require __DIR__ . '/app/auth.php';

require __DIR__ . '/app/users.php';

require __DIR__ . '/app/profiles.php';

require __DIR__ . '/app/messages.php';

require __DIR__ . '/app/queues.php';

require __DIR__ . '/app/devices.php';

require __DIR__ . '/app/commands.php';