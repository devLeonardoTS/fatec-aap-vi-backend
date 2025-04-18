<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  // Relationships

  // A user can have one profile
  public function profile()
  {
    return $this->hasOne(Profile::class);
  }

  // A user can have many addresses
  public function addresses()
  {
    return $this->hasMany(Address::class);
  }

  // A user can have many devices
  public function devices()
  {
    return $this->hasMany(Device::class);
  }

  public function devices_commands()
  {
    return $this->hasManyThrough(
      DeviceCommand::class,
      Device::class,
      'user_id', // Foreign key on devices table...
      'device_id', // Foreign key on device_commands table...
      'id', // Local key on users table...
      'id'  // Local key on devices table...
    );
  }

  public function devices_metrics()
  {
    return $this->hasManyThrough(
      DeviceMetrics::class,
      Device::class,
      'user_id',         // Foreign key on Device table...
      'device_id',       // Foreign key on DeviceMetrics table...
      'id',              // Local key on User table...
      'id'               // Local key on Device table...
    );
  }
}