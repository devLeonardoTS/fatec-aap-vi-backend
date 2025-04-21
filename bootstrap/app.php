<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {

    $middleware->append(\App\Http\Middleware\ForceJsonResponse::class);

    $middleware->validateCsrfTokens(except: [
      'api/*',
    ]);

  })
  ->withExceptions(function (Exceptions $exceptions) {

    // Handle "Not Found" routes on api routes.
    // $exceptions->render(function (NotFoundHttpException $e, Illuminate\Http\Request $request) {
    //   if ($request->is('api/*')) {
    //     return response()->json([
    //       'message' => 'Resource not found.',
    //       'error' => 'NOT_FOUND',
    //     ], 404);
    //   }
    // });
  
  })->create();