<?php

use App\Http\Controllers\ExampleController;

// Exemplo de rota sem agrupamento
Route::get(
  '/base-example',
  [ExampleController::class, 'index']
)
  ->name('base-example.index');

// Exemplo de rotas agrupadas (recomendado)
Route::prefix('group-example')->name('group-example.')->group(function () {

  Route::get('/', [ExampleController::class, 'welcome'])->name('welcome');
  Route::get('/list', [ExampleController::class, 'list'])->name('list');

});

// Rotas de autenticação separada em um arquivo específico (recomendado)
require __DIR__ . '/app/auth.php';

require __DIR__ . '/app/messages.php';