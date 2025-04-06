<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Command;
use Illuminate\Http\Request;

// Todo: Update this controller from Queues to Commands.

class QueuesController extends Controller
{
  public function openCloseDevice(Request $request)
  {

    $device = $request->query('device');

    $event = Command::where('device', $device)->first();

    if (!$event) {
      return response()->json([
        'message' => 'No event found on the queue for the provided device.'
      ], 404);
    }

    $event->delete();

    return response()->json([
      'data' => [
        'message' => "Event handled successfully!",
        'event' => $event,
      ]
    ]);

  }

  public function captureWaterFlowInfo(Request $request)
  {

    $fields = $request->only([
      'device',
      'average_water_flow',
    ]);

    Message::create([
      'content' => "O dispositivo \"{$fields['device']}\" capturou um fluxo de água mediano de {$fields['average_water_flow']} m3/h",
    ]);

    return response()->json([
      'data' => [
        'message' => 'Captura de dados realizada com sucesso',
        'average_water_flow' => $fields['average_water_flow'] ?? null
      ],
    ]);

  }

  public function dispatchDeviceCommand(Request $request)
  {

    $exists = Command::where('command', $request->input('command'))->exists();

    if ($exists) {
      return response()->json([
        'message' => 'The command is already in the queue.'
      ], 409);
    }

    $fields = $request->only([
      'device',
      'command',
    ]);

    $event = Command::create($fields);

    return response()->json([
      'data' => [
        'message' => 'Event created successfully.',
        'event' => $event
      ]
    ]);

  }

}