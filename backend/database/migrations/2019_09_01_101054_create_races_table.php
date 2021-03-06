<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRacesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('races', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->float('best_time')->nullable();
			$table->float('finished_time')->nullable();
			$table->string('status')->nullable();
			$table->integer('current_time')->nullable();
			$table->integer('race_meter')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('races');
	}
}
