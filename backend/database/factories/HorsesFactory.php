<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Horses;
use Faker\Generator as Faker;

$factory->define(Horses::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->safeColorName,
        'speed' => $faker->numberBetween($min = 5, $max = 10),
        'strength' => $faker->numberBetween($min = 1, $max = 10),
        'endurance' => $faker->numberBetween($min = 1, $max = 10),
        'created_at' => now(),
        'updated_at' => now()
    ];
});
