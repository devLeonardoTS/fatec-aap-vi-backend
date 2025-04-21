<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\DeviceCommand;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommandsController extends Controller
{

  public function executeCommand(DeviceCommand $command)
  {
    $command->update(['executed_at' => now()]);

    $command->device->update([
      'status' => match ($command->command) {
        'open' => 'Aberto',
        'close' => 'Fechado',
        default => $command->device->status,
      }
    ]);

    return response()->json([
      'data' => $command->load('device'),
      'message' => 'Comando executado com sucesso.',
    ]);
  }
}