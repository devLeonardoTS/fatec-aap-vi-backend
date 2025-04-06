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
      'data' => $device,
      'message' => 'Dispositivo encontrado.',
    ]);

  }

  public function store(Request $request)
  {
    $device = Device::create();

    return response()->json([
      'data' => $device,
      'message' => 'Dispositivo registrado com sucesso.',
    ], Response::HTTP_CREATED);
  }
}