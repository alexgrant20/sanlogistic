<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('vehicle_images', function (Blueprint $table) {
      $table->id();
      $table->foreignId('vehicle_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->string('image')->nullable();
      $table->string('type')->nullable();
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('vehicle_images');
  }
};