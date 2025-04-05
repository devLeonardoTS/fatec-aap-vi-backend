<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
  public function index()
  {
    $profiles = Profile::all();
    return response()->json([
      'data' => [
        'messages' => $profiles
      ]
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'full_name' => 'required|string|min:3',
    ], [
      'full_name.required' => 'O campo do nome completo é obrigatório.',
      'full_name.string' => 'O campo do nome completo deve ser uma string.',
      'full_name.min' => 'O campo do nome completo deve ter pelo menos 3 caracteres.',
    ]);

    $profile = Profile::create([
      'full_name' => $request->input('full_name')
    ]);

    return response()->json([
      'data' => [
        'message' => $profile,
        'success' => 'Perfil criado com sucesso.'
      ]
    ]);
  }

  public function show(Message $message)
  {
    return response()->json([
      'data' => [
        'message' => $message,
      ]
    ]);
  }

  public function patch(Request $request, Message $profile)
  {
    $request->validate([
      'full_name' => 'sometimes|string|min:3',
    ], [
      'full_name.sometimes' => 'O campo do nome completo é obrigatório.',
      'full_name.string' => 'O campo do nome completo deve ser uma string.',
      'full_name.min' => 'O campo do nome completo deve ter pelo menos 3 caracteres.',
    ]);

    $profile->update([
      'full_name' => $request->input('full_name')
    ]);

    return response()->json([
      'data' => [
        'profile' => $profile,
        'success' => 'Perfil atualizado com sucesso.'
      ]
    ]);
  }

  public function destroy(Message $message)
  {
    $message->delete();

    return response()->json([
      'data' => [
        'success' => 'Message deleted successfully.'
      ]
    ]);
  }


  public function actions()
  {
    $endpoints = [
      'index' => [
        'method' => 'GET',
        'url' => route('profiles.index'),
        'description' => 'Retorna uma lista de perfis.',
      ],
      'store' => [
        'method' => 'POST',
        'url' => route('profiles.store'),
        'description' => 'Cria um novo perfil.',
      ],
      // 'show' => [
      //   'method' => 'GET',
      //   'url' => route('messages.show', ['message' => ':id']),
      //   'description' => 'Retorna uma mensagem específica.',
      // ],
      // 'patch' => [
      //   'method' => 'PATCH',
      //   'url' => route('messages.patch', ['message' => ':id']),
      //   'description' => 'Atualiza uma mensagem.',
      // ],
      // 'destroy' => [
      //   'method' => 'DELETE',
      //   'url' => route('messages.destroy', ['message' => ':id']),
      //   'description' => 'Remove uma mensagem.',
      // ],
    ];

    return response()->json([
      'data' => $endpoints
    ]);
  }
}