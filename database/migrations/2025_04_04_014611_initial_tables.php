<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {

    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn('name');
      $table->string('role')->default('Usuário');
      $table->boolean('is_active')->default(true);
    });

    Schema::create('marketing_leads', function (Blueprint $table) {
      $table->id();
      $table->string('email');
      $table->boolean('is_active')->default(true);
      $table->timestamp('last_email_sent_at')->nullable();
      $table->timestamps();
    });

    Schema::create('profiles', function (Blueprint $table) {
      $table->id();
      $table->string('full_name')->nullable();

      $table->timestamps();

      $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

    });

    Schema::create('addresses', function (Blueprint $table) {
      $table->id();

      $table->string('phone_number')->nullable();

      $table->string('address')->nullable();
      $table->string('city')->nullable();
      $table->string('state')->nullable();
      $table->string("identifier")->nullable();
      $table->string("reference")->nullable();
      $table->string('zip_code')->nullable();
      $table->string('country')->nullable();

      $table->timestamps();

      $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    });

    Schema::create('messages', function (Blueprint $table) {
      $table->id();
      $table->longText('content');
      $table->timestamps();
    });

    Schema::create("commands", function (Blueprint $table) {
      $table->id();
      $table->string("device");
      $table->string("command");
      $table->timestamps();
    });

    Schema::create('devices', function (Blueprint $table) {
      $table->id();
      $table->string('token');

      $table->string('name')->nullable();
      $table->string('description')->nullable();
      $table->string('status')->default('Aberto'); // Aberto, Fechado

      $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

      $table->timestamps();
    });

    Schema::create('device_commands', function (Blueprint $table) {
      $table->id();
      $table->string('command');
      $table->timestamp('executed_at')->nullable();
      $table->timestamp('execute_after')->nullable();
      $table->timestamps();

      $table->foreignId('device_id')->constrained()->onDelete('cascade');

    });

    Schema::create('device_metrics', function (Blueprint $table) {
      $table->id();
      $table->decimal('water_flow', 10, 4);
      $table->timestamps();

      $table->foreignId('device_id')->constrained()->onDelete('cascade');

    });

  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::disableForeignKeyConstraints();

    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn('role');
    });
    Schema::dropIfExists('marketing_leads');
    Schema::dropIfExists('profiles');
    Schema::dropIfExists('addresses');
    Schema::dropIfExists('messages');
    Schema::dropIfExists('commands');
    Schema::dropIfExists('devices');
    Schema::dropIfExists('device_commands');
    Schema::dropIfExists('device_metrics');


    Schema::enableForeignKeyConstraints();
  }
};