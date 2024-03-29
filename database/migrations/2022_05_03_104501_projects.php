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
    Schema::table('projects', function (Blueprint $table) {
      $table->foreignId('created_by')->nullable()->references('id')->on('users')->restrictOnDelete()->cascadeOnUpdate();
      $table->foreignId('updated_by')->nullable()->references('id')->on('users')->restrictOnDelete()->cascadeOnUpdate();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('projects', function (Blueprint $table) {
      $table->dropForeign(['created_by', 'updated_by']);
      $table->dropColumn(['created_by', 'updated_by']);
    });
  }
};