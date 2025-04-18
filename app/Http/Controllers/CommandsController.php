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

    $command->executed_at = now();
    $command->save();
    $command->load('device');

    return response()->json([
      'data' => $command,
      'message' => 'Comando executado com sucesso.',
    ]);

  }
}