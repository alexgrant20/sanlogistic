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
    Schema::create('people', function (Blueprint $table) {
      $table->id();
      $table->foreignId('project_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('department_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('area_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('address_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      $table->string('name');
      $table->string('image');
      $table->string('place_of_birth');
      $table->date('date_of_birth');
      $table->string('phone_number');
      $table->date('joined_at');
      $table->text('note')->nullable();
      $table->tinyInteger('active')->default(1);
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('people');
  }
};