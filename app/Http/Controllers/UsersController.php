<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'full_name' => 'required|string|min:3',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ], [
      'full_name.required' => 'O campo do nome completo é obrigatório.',
      'full_name.string' => 'O campo do nome completo deve ser uma string.',
      'full_name.min' => 'O campo do nome completo deve ter pelo menos :min caracteres.',
      'email.required' => 'O campo do e-mail é obrigatório.',
      'email.string' => 'O campo do e-mail deve ser uma string.',
      'email.email' => 'O campo do e-mail deve ser um e-mail.',
      'email.max' => 'O campo do e-mail deve ter no máximo :max caracteres.',
      'email.unique' => 'O e-mail informado já está em uso.',
      'password.required' => 'O campo da senha é obrigatório.',
      'password.string' => 'O campo da senha deve ser uma string.',
      'password.min' => 'O campo da senha deve ter pelo menos :min caracteres.',
      'password.confirmed' => 'As senhas informadas não conferem.',
    ]);

    \DB::beginTransaction();

    try {
      $user = User::create([
        'email' => $request->email,
        'password' => $request->password,
      ]);

      // Create profile along with user
      $user->profile()->create(["full_name" => $request->full_name]);

      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();

      \Log::error($e->getMessage());

      return response()->json([
        'message' => 'Erro ao criar o usuário. Ocorreu um erro na operação do banco de dados.',
        'error' => $e->getMessage(),
      ], 500);

    }

    return response()->json([
      'data' => $user->load('profile'),
      'message' => 'Usuário criado com sucesso.',
    ], 201);
  }
}