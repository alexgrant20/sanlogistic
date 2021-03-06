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
    Schema::create('activities', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('vehicle_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('project_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('departure_location_id')->references('id')->on('addresses')->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('arrival_location_id')->references('id')->on('addresses')->restrictOnDelete()->cascadeOnUpdate();
      $table->date('do_date');
      $table->string('do_number');
      $table->string('do_image');
      $table->dateTime('departure_date')->useCurrent();
      $table->bigInteger('departure_odo');
      $table->string('departure_odo_image');
      $table->dateTime('arrival_date')->nullable();
      $table->bigInteger('arrival_odo')->nullable();
      $table->string('arrival_odo_image')->nullable();
      $table->string('bbm_image')->nullable();
      $table->string('toll_image')->nullable();
      $table->string('retribution_image')->nullable();
      $table->string('parking_image')->nullable();
      $table->string('type')->default('TBD');
      $table->string('start_lat')->nullable();
      $table->string('start_lon')->nullable();
      $table->string('start_loc')->nullable();
      $table->string('end_lat')->nullable();
      $table->string('end_lon')->nullable();
      $table->string('end_loc')->nullable();
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
    Schema::dropIfExists('activities');
  }
};