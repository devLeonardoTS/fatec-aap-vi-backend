<?php

namespace App\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Use esse middleware apenas se for absolutamente necessário.

class JsonOnlyForApiRoutes
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if (!$request->wantsJson()) {

      return response()->json([
        'error' => 'Not Acceptable',
        'message' => 'Please request with HTTP header: Accept: application/json'
      ], Response::HTTP_NOT_ACCEPTABLE);

    }

    return $next($request);
  }
}