<?php

namespace Database\Seeders;

use App\Constants\UserRoles;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::factory()->create([
      'name' => 'Administrador',
      'email' => 'admin@admin.com',
      'password' => 'admin123',
      'role' => UserRoles::ADMIN
    ]);
  }
}