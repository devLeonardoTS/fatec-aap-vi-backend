<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingLead;

class MarketingLeadsController extends Controller
{
  public function index(Request $request)
  {

    $limit = $request->query('limit') ?? 5;

    $query = $request->query->all();
    $filters = $query['filters'] ?? [];
    $sortBy = $query['sortBy'] ?? [];
    $direction = $query['direction'] ?? "desc";

    $resource = MarketingLead::filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->paginate($limit);

    return response()->json($resource);

  }

  public function store(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email|max:255',
    ], [
      'email.required' => 'O campo email é obrigatório.',
      'email.email' => 'O campo email deve ser um endereço de email válido.',
    ]);

    $lead = MarketingLead::create([
      'email' => $request->input('email')
    ]);

    return response()->json([
      'data' => [
        'lead' => $lead,
        'message' => 'Lead criado com sucesso.'
      ],
    ]);

  }
}