<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Races;
use Faker\Generator as Faker;

$factory->define(Races::class, function (Faker $faker) {
	return [
		'best_time' => $faker->unixTime($max = 'now'),
		'status' => 'Ongoing',
		'current_time' => 0,
		'completed_horse_count' => 0,
		'created_at' => now(),
		'updated_at' => now()
	];
});
