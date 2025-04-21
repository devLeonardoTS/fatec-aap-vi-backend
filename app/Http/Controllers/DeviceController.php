<?php

namespace App\Http\Controllers;

use App\Constants\UserRoles;
use App\Models\Device;
use App\Models\DeviceMetric;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{

  public function getDevices(Request $request)
  {
    $limit = $request->query('limit') ?? 10;

    $query = $request->query->all();
    $filters = $query['filters'] ?? [];
    $sortBy = $query['sortBy'] ?? [];
    $direction = $query['direction'] ?? "desc";

    $resource = Device::filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->latest()
      ->paginate($limit);

    return response()->json($resource);
  }

  public function getAnalytics(Request $request)
  {
    $user = $request->user();

    // Get all device IDs for the authenticated user
    $deviceIds = $user->devices()->pluck('id');

    // Get metrics for today
    $todayMetrics = DeviceMetric::whereIn('device_id', $deviceIds)
      ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
      ->get();

    // Get metrics for last hour
    $lastHourMetrics = DeviceMetric::whereIn('device_id', $deviceIds)
      ->whereBetween('created_at', [now()->subHour(), now()])
      ->get();

    $analytics = [
      'total_devices' => $deviceIds->count(),
      'total_water_flow_today' => $todayMetrics->sum(fn($m) => (float) $m->water_flow),
      'average_water_flow_last_hour' => $lastHourMetrics->avg(fn($m) => (float) $m->water_flow),
    ];

    // Monthly aggregation
    $monthlyData = DeviceMetric::whereIn('device_id', $deviceIds)
      ->whereYear('created_at', now()->year)
      ->get()
      ->groupBy(fn($metric) => $metric->created_at->format('F'))
      ->map(fn($group) => [
        'month' => $group->first()->created_at->format('F'),
        'flow' => round($group->sum(fn($m) => (float) $m->water_flow), 2),
      ])
      ->values()
      ->toArray();

    $analytics['monthly_flow_sum'] = $monthlyData;

    return response()->json([
      'data' => $analytics,
      'message' => 'Análise realizada com sucesso.',
    ]);
  }

  public function getActions(Request $request)
  {

    $endpoints = [
      'getDevices' => [
        'method' => 'GET',
        'url' => route('devices.getDevices'),
        'description' => 'Retorna uma lista de dispositivos.',
      ],
      'getAnalytics' => [
        'method' => 'GET',
        'url' => route('devices.getAnalytics'),
        'description' => 'Retorna análise para dispositivos.',
      ],
      'showDevice' => [
        'method' => 'GET',
        'url' => route('devices.showDevice', ['device' => ':token']),
        'description' => 'Retorna um dispositivo específico pelo token.',
      ],
      'showDeviceAnalytics' => [
        'method' => 'GET',
        'url' => route('devices.showDeviceAnalytics', ['device' => ':token']),
        'description' => 'Retorna análise para um dispositivo específico.',
      ],
      'showDeviceCommands' => [
        'method' => 'GET',
        'url' => route('devices.showDeviceCommands', ['device' => ':token']),
        'description' => 'Retorna comandos para um dispositivo específico.',
      ],
      'storeDeviceCommand' => [
        'method' => 'POST',
        'url' => route('devices.storeDeviceCommand', ['device' => ':token']),
        'description' => 'Armazena um comando para um dispositivo específico.',
      ],
      "storeDeviceMetrics" => [
        'method' => 'POST',
        'url' => route('devices.storeDeviceMetrics', ['device' => ':token']),
        'description' => 'Armazena as métricas de vazão de água "water_flow" para um dispositivo específico.',
      ],
      'storeDevice' => [
        'method' => 'POST',
        'url' => route('devices.storeDevice'),
        'description' => 'Armazena um novo dispositivo.',
      ],
      'patchDeviceCommand' => [
        'method' => 'PATCH',
        'url' => route('devices.patchDeviceCommand', ['device' => ':token', 'command' => ':id']),
        'description' => 'Atualiza um comando específico de um dispositivo.',
      ],
      'patchDevice' => [
        'method' => 'PATCH',
        'url' => route('devices.patchDevice', ['device' => ':token']),
        'description' => 'Atualiza um dispositivo específico.',
      ],
      'destroyDeviceCommand' => [
        'method' => 'DELETE',
        'url' => route('devices.destroyDeviceCommand', ['device' => ':token', 'command' => ':id']),
        'description' => 'Remove um comando específico de um dispositivo.',
      ],
      'destroyAllDeviceCommands' => [
        'method' => 'DELETE',
        'url' => route('devices.destroyAllDeviceCommands', ['device' => ':token']),
        'description' => 'Remove todos os comandos de um dispositivo específico.',
      ],
    ];

    return response()->json([
      'data' => $endpoints
    ]);

  }

  public function showDeviceAnalytics(Request $request, Device $device)
  {
    $user = $request->user();

    // Get all device IDs for the authenticated user
    $deviceId = $device->id;

    // Get latest metric for the device
    $currentFlow = DeviceMetric::where('device_id', $deviceId)
      ->latest()
      ->first();

    $startOfDayUtc = now('America/Sao_Paulo')->startOfDay()->timezone('UTC');
    $endOfDayUtc = now('America/Sao_Paulo')->endOfDay()->timezone('UTC');

    // Get metrics for today
    $todayMetrics = DeviceMetric::where('device_id', $deviceId)
      ->whereBetween('created_at', [$startOfDayUtc, $endOfDayUtc])
      ->get();

    // Get metrics for last hour
    $lastHourMetrics = DeviceMetric::where('device_id', $deviceId)
      ->whereBetween('created_at', [now()->subHour(), now()])
      ->get();

    $analytics = [
      'device' => $device,
      'current_flow' => $currentFlow,
      'total_water_flow_today' => $todayMetrics->sum(fn($m) => (float) $m->water_flow),
      'average_water_flow_last_hour' => $lastHourMetrics->avg(fn($m) => (float) $m->water_flow),
    ];

    // Monthly aggregation
    $monthlyData = DeviceMetric::where('device_id', $deviceId)
      ->whereYear('created_at', now()->year)
      ->get()
      ->groupBy(fn($metric) => $metric->created_at->format('F'))
      ->map(fn($group) => [
        'month' => $group->first()->created_at->format('F'),
        'flow' => round($group->sum(fn($m) => (float) $m->water_flow), 2),
      ])
      ->values()
      ->toArray();

    $analytics['monthly_flow_sum'] = $monthlyData;

    return response()->json([
      'data' => $analytics,
      'message' => 'Análise realizada com sucesso.',
    ]);
  }

  public function showDeviceCommands(Request $request, Device $device)
  {
    $limit = $request->query('limit') ?? 10;
    $filters = $request->query('filters') ?? [];
    $sortBy = $request->query('sortBy') ?? [];
    $direction = $request->query('direction') ?? "desc";

    $commands = $device->commands()
      ->latest()
      ->filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->paginate($limit);

    return response()->json([
      'data' => $commands,
      'message' => 'Comandos encontrados.',
    ]);
    // Implement device commands logic here
  }

  public function showDeviceMetrics(Request $request, Device $device)
  {
    $limit = $request->query('limit') ?? 10;
    $filters = $request->query('filters') ?? [];
    $sortBy = $request->query('sortBy') ?? [];
    $direction = $request->query('direction') ?? "desc";

    $metrics = $device->metrics()
      ->latest()
      ->filter($filters)
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->paginate($limit);

    return response()->json([
      'data' => $metrics,
      'message' => 'Métricas encontradas.',
    ]);

  }

  public function storeDeviceCommand(Request $request, Device $device)
  {
    $fields = $request->validate([
      'command' => 'required|string|in:open,close,verify',
      'execute_after' => 'sometimes|date',
    ], [
      'command.required' => 'O campo commando é obrigatório.',
      'command.string' => 'O campo command deve ser uma string.',
      'command.in' => 'O campo command deve ser um dos seguintes valores: open, close, verify.',
      'execute_after.date' => 'O campo execute_after deve ser uma data.',
    ]);

    $command = $device->commands()->create($fields);

    return response()->json([
      'data' => $command,
      'message' => 'Comando criado com sucesso.',
    ]);
  }

  public function storeDeviceMetrics(Request $request, Device $device)
  {
    $fields = $request->validate([
      'water_flow' => 'required|numeric|min:0',
    ], [
      'water_flow.required' => 'O campo "water_flow" é obrigatório.',
      'water_flow.numeric' => 'O campo "water_flow" deve ser um número.',
      'water_flow.min' => 'O campo "water_flow" deve ser maior ou igual a zero.',
    ]);

    $metric = $device->metrics()->create($fields);

    return response()->json([
      'data' => $metric,
      'message' => 'Fluxo de água registrado com sucesso.',
    ]);

  }

  public function patchDeviceCommand(Request $request, Device $device, $command)
  {

    $command = $device->commands()->find($command);

    if (!$command) {
      return response()->json([
        'message' => 'Comando não encontrado.',
      ], 404);
    }

    $fields = $request->validate([
      'command' => 'sometimes|string|in:open,close,verify',
      'executed_at' => 'sometimes|date',
    ], [
      'command.string' => 'O campo commando deve ser uma string.',
      'command.in' => 'O campo command deve ser um dos seguintes valores: open, close, verify.',
      'executed_at.date' => 'O campo executed_at deve ser uma data.',
    ]);

    $command->update($fields);

    return response()->json([
      'data' => $command,
    ]);

  }

  public function patchDevice(Request $request, Device $device)
  {
    $user = $request->user();

    // Check if the user is an admin or the owner of the resource
    if ($user->role !== UserRoles::ADMIN && $user->id !== $device->user_id) {
      return response()->json([
        'message' => 'Você não tem permissão para atualizar este dispositivo.',
      ], Response::HTTP_FORBIDDEN);
    }

    $fields = $request->validate([
      'name' => 'sometimes|string|max:255',
      'description' => 'sometimes|string|max:255',
    ], [
      'name.string' => 'O campo nome deve ser uma string.',
      'name.max' => 'O campo nome deve ter no máximo 255 caracteres.',
      'description.string' => 'O campo descrição deve ser uma string.',
      'description.max' => 'O campo descrição deve ter no máximo 255 caracteres.',
    ]);

    $device->update($fields);

    return response()->json([
      'data' => $device,
      'message' => 'Dispositivo atualizado com sucesso.',
    ]);
  }

  public function destroyDeviceCommand(Request $request, Device $device, $command)
  {
    $command = $device->commands()->find($command);

    if (!$command) {
      return response()->json([
        'message' => 'Comando não encontrado.',
      ], 404);
    }

    $command->delete();

    return response()->json([
      'data' => $command,
      'message' => 'Comando removido com sucesso.',
    ]);
    // Implement destroy device command logic here
  }

  public function destroyAllDeviceCommands(Request $request, Device $device)
  {
    $device->commands()->where('executed_at', null)->delete();

    return response()->json([
      'message' => 'Todos os comandos não executados foram removidos com sucesso.',
    ]);

    // Implement destroy all device commands logic here
  }

  public function showDevice(Device $device)
  {

    $device->load([
      'user',
      'metrics' => function ($query) {
        $query->latest()->take(10);
      },
      'commands' => function ($query) {
        $query->latest()->take(10);
      },
    ]);

    return response()->json([
      'data' => $device,
      'message' => 'Dispositivo encontrado.',
    ]);

  }

  public function storeDevice(Request $request)
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