<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueEvent extends Model
{

  protected $table = 'queue_events';

  protected $fillable = [
    'device',
    'command',
  ];

}