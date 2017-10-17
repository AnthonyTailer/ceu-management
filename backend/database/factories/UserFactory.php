<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'fullName' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('segredo123'),
        'remember_token' => str_random(10),
        'rg' => $faker->unique()->numberBetween(1000000000,9999999999),
        'cpf' => $faker->numerify("###########"),
        'genre' => $faker->randomElement(["M", "F"]),
        'id_course' => random_int(\DB::table('courses')->min('id'), \DB::table('courses')->max('id')),
        'is_bse_active' => rand(0,1),
        'is_admin' => rand(0,1),
        'age' => rand(15,85),
        'registration' => $faker->unique()->numberBetween(100000000,999999999)
    ];
});
