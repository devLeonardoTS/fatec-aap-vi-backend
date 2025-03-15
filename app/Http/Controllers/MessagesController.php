<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessagesController extends Controller
{
  public function index()
  {
    $messages = Message::all();
    return response()->json([
      'data' => [
        'messages' => $messages
      ]
    ]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'content' => 'required',
    ], [
      'content.required' => 'O campo de conteúdo é obrigatório.',
    ]);

    $message = Message::create([
      'content' => $request->input('content')
    ]);

    return response()->json([
      'data' => [
        'message' => $message,
        'success' => 'Message created successfully.'
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

  public function patch(Request $request, Message $message)
  {
    $request->validate([
      'content' => 'required',
    ], [
      'content.required' => 'O campo de conteúdo é obrigatório.',
    ]);

    $message->update([
      'content' => $request->input('content')
    ]);

    return response()->json([
      'data' => [
        'message' => $message,
        'success' => 'Message updated successfully.'
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
        'url' => route('messages.index'),
        'description' => 'Retorna uma lista de mensagens.',
      ],
      'store' => [
        'method' => 'POST',
        'url' => route('messages.store'),
        'description' => 'Cria uma nova mensagem.',
      ],
      'show' => [
        'method' => 'GET',
        'url' => route('messages.show', ['message' => ':id']),
        'description' => 'Retorna uma mensagem específica.',
      ],
      'patch' => [
        'method' => 'PATCH',
        'url' => route('messages.patch', ['message' => ':id']),
        'description' => 'Atualiza uma mensagem.',
      ],
      'destroy' => [
        'method' => 'DELETE',
        'url' => route('messages.destroy', ['message' => ':id']),
        'description' => 'Remove uma mensagem.',
      ],
    ];

    return response()->json([
      'data' => $endpoints
    ]);
  }
}