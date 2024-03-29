<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::dropIfExists('roles');
  }

  public function down()
  {
    Schema::create('roles', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
    });
  }
};