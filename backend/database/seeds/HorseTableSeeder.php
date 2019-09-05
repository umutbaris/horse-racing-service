<?php

use Illuminate\Database\Seeder;
use App\Models\Horses;

class HorsesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$horses = factory(Horses::class, 8)->create();
	}
}
