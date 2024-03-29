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
    Schema::create('person_documents', function (Blueprint $table) {
      $table->id();
      $table->foreignId('person_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
      // For SIM (Not Constrained)
      $table->foreignId('specialID')->nullable();
      $table->string('type');
      $table->string('number')->nullable();
      $table->string('address')->nullable();
      $table->string('image')->nullable();
      $table->date('expire')->nullable();
      $table->tinyInteger('active')->default(1);
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
    Schema::dropIfExists('person_documents');
  }
};