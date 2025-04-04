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
      $table->string('role')->default('user');
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
      $table->string('full_name')->nullable();

      $table->string('phone_number')->nullable();

      $table->string('address')->nullable();
      $table->string('city')->nullable();
      $table->string('state')->nullable();
      $table->string("identifier")->nullable();
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

    Schema::enableForeignKeyConstraints();
  }
};