<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{

  public function index()
  {
    return response()->json([
      'message' => 'Boas vindas a API Backend do Projeto de AAP VI!',
      'group_example_url' => route('group-example.welcome')
    ]);
  }

  public function welcome()
  {
    return response()->json([
      'message' => 'Você acessou uma rota que está dentro de um agrupamento!',
      'names_list_url' => route('group-example.list'),
      'base_url' => route('base-example.index')
    ]);
  }

  public function list()
  {
    return response()->json([
      'names' => [
        'Pedro',
        'Alice',
        'Roberto',
        'Felipe',
        'Maria',
        'Rodrigo',
        'Sofia',
        'Guilherme'
      ],
      'group_example_url' => route('group-example.welcome')
    ]);
  }
}