<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropConstrainedForeignId(['role_id']);
      $table->dropColumn(['role_id']);
    });
  }

  public function down()
  {
    Schema::table('users', function (Blueprint $table) {
      $table->foreignId('role_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
    });
  }
};