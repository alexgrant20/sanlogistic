<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.p
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activities_statuses', function (Blueprint $table) {
			$table->id();
			$table->foreignId('activity_id');
			$table->string('status');
			$table->timestamp('created_at')->useCurrent();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('activities_statuses');
	}
};