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
    Schema::table('people', function (Blueprint $table) {
      $table->bigInteger('project_id')->unsigned()->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('people', function (Blueprint $table) {
      $table->bigInteger('project_id')->unsigned()->nullable(false)->change();
    });
  }
};