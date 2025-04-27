<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketsController extends Controller
{
  public function index(Request $request)
  {
    $limit = $request->query('limit') ?? 10;

    $query = $request->query->all();
    $filters = $query['filters'] ?? [];
    $sortBy = $query['sortBy'] ?? [];
    $direction = $query['direction'] ?? "desc";

    $resource = Ticket::filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->latest()
      ->paginate($limit);

    return response()->json($resource);
  }

  public function listComments(Request $request, Ticket $ticket)
  {
    $limit = $request->query('limit') ?? 10;

    $query = $request->query->all();
    $filters = $query['filters'] ?? [];
    $sortBy = $query['sortBy'] ?? [];
    $direction = $query['direction'] ?? "desc";

    $resource = Ticket::comments()
      ->filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->latest()
      ->paginate($limit);

    return response()->json($resource);
  }

  public function actions()
  {
    // Logic for retrieving actions related to tickets
    $actions = [
      'index' => [
        'method' => 'GET',
        'url' => route('tickets.index'),
        'description' => 'Returns a list of tickets.'
      ],
      'show' => [
        'method' => 'GET',
        'url' => route('tickets.show', ['ticket' => ':id']),
        'description' => 'Returns a specific ticket.'
      ],
      'showComments' => [
        'method' => 'GET',
        'url' => route('tickets.showComments', ['ticket' => ':id']),
        'description' => 'Returns a list of comments for a specific ticket.'
      ],
      'store' => [
        'method' => 'POST',
        'url' => route('tickets.store'),
        'description' => 'Creates a new ticket.'
      ],
      'storeComment' => [
        'method' => 'POST',
        'url' => route('tickets.storeComment', ['ticket' => ':id']),
        'description' => 'Creates a new comment for a ticket.'
      ],
      'patch' => [
        'method' => 'PATCH',
        'url' => route('tickets.patch', ['ticket' => ':id']),
        'description' => 'Updates a ticket.'
      ],
      'destroy' => [
        'method' => 'DELETE',
        'url' => route('tickets.destroy', ['ticket' => ':id']),
        'description' => 'Deletes a ticket.'
      ],
    ];
    return response()->json(['data' => $actions]);
  }

  public function show(Ticket $ticket)
  {
    // Logic to return a specific ticket
    return response()->json([
      "data" => $ticket
    ]);
  }

  public function showComments(Request $request, Ticket $ticket)
  {

    $limit = $request->query('limit') ?? 10;

    $query = $request->query->all();
    $filters = $query['filters'] ?? [];
    $sortBy = $query['sortBy'] ?? [];
    $direction = $query['direction'] ?? "asc";

    $resource = $ticket->comments()
      ->with('user.profile')
      ->filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      // ->latest()
      ->paginate($limit);

    return response()->json($resource);
  }

  public function store(Request $request)
  {

    $validated = $request->validate([
      'title' => 'sometimes|string|max:255',
      'status' => 'sometimes|string|in:Aberto,Fechado',
      'description' => 'sometimes|string|max:65535',
    ], [
      'title.string' => 'O título precisa ser uma string.',
      'title.max' => 'O título precisa ter no máximo 255 caracteres.',
      'status.string' => 'O status precisa ser uma string.',
      'status.in' => 'O status precisa ser "Aberto" ou "Fechado".',
      'description.string' => 'A descrição precisa ser uma string.',
      'description.max' => 'A descrição pode ter no máximo 65535 caracteres.',
    ]);

    // Logic to create a new ticket
    $ticket = Ticket::create($validated);
    $ticket->user()->associate(auth()->user());
    $ticket->save();

    return response()->json($ticket, 201);
  }

  public function storeComment(Request $request, Ticket $ticket)
  {
    $validated = $request->validate([
      'comment' => 'required|string|max:65535',
    ], [
      'comment.required' => 'O comentário é obrigatório.',
      'comment.string' => 'O comentário precisa ser uma string.',
      'comment.max' => 'O comentário pode ter no máximo 65535 caracteres.',
    ]);

    $validated["user_id"] = auth()->id();

    $comment = $ticket->comments()->create($validated);

    return response()->json($comment, 201);
  }


  public function patch(Request $request, Ticket $ticket)
  {

    $user = $request->user();

    $validated = $request->validate([
      'title' => 'sometimes|string|max:255',
      'status' => 'sometimes|string|in:Aberto,Fechado',
      'description' => 'sometimes|string|max:65535',
    ], [
      'title.string' => 'O título precisa ser uma string.',
      'title.max' => 'O título precisa ter no máximo 255 caracteres.',
      'status.string' => 'O status precisa ser uma string.',
      'status.in' => 'O status precisa ser "Aberto" ou "Fechado".',
      'description.string' => 'A descrição precisa ser uma string.',
      'description.max' => 'A descrição pode ter no máximo 65535 caracteres.',
    ]);

    $validated["user_id"] = $user->id;

    $ticket->update($validated);

    return response()->json($ticket);
  }

  public function destroy(Ticket $ticket)
  {
    // Logic to delete a ticket
    $ticket->delete();
    return response()->json(null, 204);
  }
}