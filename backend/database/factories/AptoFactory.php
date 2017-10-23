<?php

use Faker\Generator as Faker;

$factory->define(App\Apartament::class, function (Faker $faker) {
    return [
        'number'=> $faker->unique()->numberBetween(1001,5010),
        'capacity' => $faker->randomElement([2, 6]),
        'vacancy' => $faker->numberBetween(0,1),
        'id_block' => random_int(\DB::table('blocks')->min('id'), \DB::table('blocks')->max('id'))
    ];
});
