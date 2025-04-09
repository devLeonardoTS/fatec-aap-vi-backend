<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{

  public function index(Request $request)
  {

    $limit = $request->query('limit') ?? 5;

    $query = $request->query->all();
    $filters = $query['filters'] ?? [];
    $sortBy = $query['sortBy'] ?? [];
    $direction = $query['direction'] ?? "desc";

    $resource = Device::filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->paginate($limit);

    return response()->json($resource);

  }

  public function show(Device $device)
  {

    return response()->json([
      'data' => $device->load(['user']),
      'message' => 'Dispositivo encontrado.',
    ]);

  }

  public function store(Request $request)
  {
    \DB::beginTransaction();

    try {
      $device = Device::create();

      if (auth()?->user()) {
        $device->user()->associate(auth()->user());
        $device->save();
      }

      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();

      \Log::error($e->getMessage());

      return response()->json([
        'message' => 'Erro ao criar o dispositivo. Ocorreu um erro na operação do banco de dados.',
        'error' => $e->getMessage(),
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return response()->json([
      'data' => $device,
      'message' => 'Dispositivo registrado com sucesso.',
    ], Response::HTTP_CREATED);
  }
}