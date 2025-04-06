<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

  public function user(Request $request)
  {
    $user = $request->user()->load('profile');

    return response()->json($user);
  }

  public function login(Request $request)
  {
    $validated = $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ], [
      'email.required' => 'O campo do e-mail é obrigatório.',
      'email.email' => 'O campo do e-mail deve ser um e-mail.',
      'password.required' => 'O campo da senha é obrigatório.',
    ]);

    $user = User::where('email', $validated['email'] ?? null)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['As credenciais estão incorretas.'],
      ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    $data = [
      'user' => $user->load('profile'),
      'token' => $token,
      'token_type' => 'Bearer',
    ];

    return response()->json([
      'data' => $data,
    ]);
  }


  public function logout(Request $request)
  {
    $request->user()->tokens()->delete();

    return response()->json([
      'message' => 'Logged out successfully!',
    ]);
  }
}