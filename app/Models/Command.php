<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{

  protected $table = 'commands';

  protected $fillable = [
    'device',
    'command',
  ];

}