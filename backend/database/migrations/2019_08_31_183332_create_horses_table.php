<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHorsesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('horses', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('name');
			$table->integer('speed');
			$table->integer('strength');
			$table->integer('endurance');
			$table->string('status');
			$table->integer('position');
			$table->float('distance_covered');
			$table->float('finished_time');
			$table->float('slow_speed');
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
		Schema::dropIfExists('horses');
	}
}