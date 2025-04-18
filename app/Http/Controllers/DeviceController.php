<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Device;
use App\Models\DeviceCommand;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceController extends Controller
{

  public function getDevices(Request $request)
  {
    $limit = $request->query('limit') ?? 5;

    $query = $request->query->all();
    $filters = $query['filters'] ?? [];
    $sortBy = $query['sortBy'] ?? [];
    $direction = $query['direction'] ?? "desc";

    $resource = Device::filter($filters)
      ->latest()
      ->filterListType($filters)
      ->sortBy($direction, $sortBy)
      ->paginate($limit);

    return response()->json($resource);
  }

  public function getAnalytics(Request $request)
  {
    $analytics = [];

    $analytics['total_devices'] = $request->user()->devices()->count();
    $analytics['total_commands'] = $request->user()->devices_commands()->whereIn('command', ['open', 'close', 'verify'])->count();

    $analytics['total_open_commands'] = $request->user()->devices_commands()->where('command', 'open')->count();
    $analytics['total_close_commands'] = $request->user()->devices_commands()->where('command', 'close')->count();
    $analytics['total_verify_commands'] = $request->user()->devices_commands()->where('command', 'verify')->count();

    $analytics['highest_water_flow_today'] = (float) $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfDay(), now()->endOfDay()])->max('water_flow');
    $analytics['highest_water_flow_week'] = (float) $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfWeek(), now()->endOfWeek()])->max('water_flow');
    $analytics['highest_water_flow_month'] = (float) $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfMonth(), now()->endOfMonth()])->max('water_flow');
    $analytics['highest_water_flow_year'] = (float) $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfYear(), now()->endOfYear()])->max('water_flow');

    $analytics['total_average_water_flow_hourly'] = $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->subHour()->startOfDay(), now()->subHour()->endOfDay()])->average('water_flow');

    $analytics['total_average_water_flow_yesterday'] = $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()])->average('water_flow');
    $analytics['total_average_water_flow_today'] = $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfDay(), now()->endOfDay()])->average('water_flow');
    $analytics['total_average_water_flow_week'] = $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfWeek(), now()->endOfWeek()])->average('water_flow');
    $analytics['total_average_water_flow_month'] = $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfMonth(), now()->endOfMonth()])->average('water_flow');
    $analytics['total_average_water_flow_year'] = $request->user()->devices_metrics()->whereBetween('device_metrics.created_at', [now()->startOfYear(), now()->endOfYear()])->average('water_flow');

    $monthlyData = $request->user()->devices_metrics()
      ->whereYear('device_metrics.created_at', now()->year)
      ->get()
      ->groupBy(fn($metric) => $metric->created_at->format('F')) // Full month name
      ->map(fn($group) => [
        'month' => $group->first()->created_at->format('F'),
        'flow' => round($group->avg('water_flow') / 60, 2),
      ])
      ->values() // reset keys
      ->toArray();

    $analytics['monthly_data'] = $monthlyData;

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
    $analytics = [];
    $analytics['total_commands'] = $device->commands()->whereIn('command', ['open', 'close', 'verify'])->count();
    $analytics['total_open_commands'] = $device->commands()->where('command', 'open')->count();
    $analytics['total_close_commands'] = $device->commands()->where('command', 'close')->count();
    $analytics['total_verify_commands'] = $device->commands()->where('command', 'verify')->count();
    $analytics['total_average_water_flow'] = $device->metrics()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->sum('average_water_flow');

    $analytics['highest_water_flow_today'] = $device->metrics()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->max('water_flow');
    $analytics['highest_water_flow_week'] = $device->metrics()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->max('water_flow');
    $analytics['highest_water_flow_month'] = $device->metrics()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->max('water_flow');
    $analytics['highest_water_flow_year'] = $device->metrics()->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->max('water_flow');

    $analytics['total_average_water_flow_today'] = $device->metrics()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->avg('average_water_flow');
    $analytics['total_average_water_flow_week'] = $device->metrics()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('average_water_flow');
    $analytics['total_average_water_flow_month'] = $device->metrics()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('average_water_flow');
    $analytics['total_average_water_flow_year'] = $device->metrics()->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])->sum('average_water_flow');

    return response()->json([
      'data' => $analytics,
      'message' => 'Análise realizada com sucesso.',
    ]);
    // Implement device analytics logic here
  }

  public function showDeviceCommands(Request $request, Device $device)
  {
    $limit = $request->query('limit') ?? 5;
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
    $limit = $request->query('limit') ?? 5;
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
    ], [
      'command.required' => 'O campo commando é obrigatório.',
      'command.string' => 'O campo command deve ser uma string.',
      'command.in' => 'O campo command deve ser um dos seguintes valores: open, close, verify.',
    ]);

    $command = $device->commands()->create($fields);

    if ($fields['command'] === 'open') {
      $device->status = 'Aberto';
    } elseif ($fields['command'] === 'close') {
      $device->status = 'Fechado';
    }

    $device->save();

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
    $fields = $request->validate([
      'name' => 'sometimes|string',
      'description' => 'sometimes|string',
    ], [
      'name.string' => 'O campo nome deve ser uma string.',
      'description.string' => 'O campo descri o deve ser uma string.',
    ]);

    $device->update($fields);

    return response()->json([
      'data' => $device,
      'message' => 'Dispositivo atualizado com sucesso.',
    ]);
    // Implement patch device logic here
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