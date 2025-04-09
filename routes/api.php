<?php

Route::middleware('auth:sanctum')->get('/protected', function (Request $request) {
  return response()->json([
    'message' => 'This is a protected route.',
    // 'user' => $request->user(),
  ]);
})->name('protected');

// Rotas de autenticação separada em um arquivo específico (recomendado)
require __DIR__ . '/app/auth.php';

require __DIR__ . '/app/users.php';

require __DIR__ . '/app/profiles.php';

require __DIR__ . '/app/messages.php';

require __DIR__ . '/app/queues.php';

require __DIR__ . '/app/devices.php';