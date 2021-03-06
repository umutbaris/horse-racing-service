<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Races;
use Faker\Generator as Faker;

$factory->define(Races::class, function (Faker $faker) {
	return [
		'best_time' => $faker->unixTime($max = 'now'),
		'status' => 'Ongoing',
		'current_time' => 0,
		'race_meter' => 0,
		'created_at' => now(),
		'updated_at' => now()
	];
});
