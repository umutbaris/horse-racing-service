<?php

use Illuminate\Database\Seeder;
use App\Models\Races;

class RacesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$races = factory(Races::class, 1)->create();
		
	}
}
