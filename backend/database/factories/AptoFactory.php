<?php

$faker = \Faker\Factory::create('pt_BR');

$factory->define(App\Apartament::class, function () use ($faker) {

    $capacity = $faker->randomElement([2, 6, 12]);
    $number = strval($faker->unique()->numberBetween(1001,5010));
    return [
        'number'=> $number,
        'capacity' => $capacity,
        'vacancy' => $capacity,
        'block' => $number[0] . $number[1],
        'building' => $number[0],
        'vacancy_type' => $faker->randomElement(['M', 'F', 'MF'])
    ];
});
