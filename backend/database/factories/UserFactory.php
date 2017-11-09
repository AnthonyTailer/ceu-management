<?php


use App\Apartament;
use App\User;

$faker = \Faker\Factory::create('pt_BR');

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

$factory->define(User::class, function () use ($faker) {

    static $password;

    $id_apto = random_int(\DB::table('apartaments')->min('id'), \DB::table('apartaments')->max('id'));

    $userApto =  DB::table('apartaments')->where('id', $id_apto)->get();

    foreach ($userApto as $user) {

        if($user->vacancy > 0){

//            $apto = Apartament::where('id', (int)$user->id)->first(); //apartamento que entrou
//            $apto->vacancy = $apto->vacancy - 1;
            \DB::table('apartaments')->where('id', $id_apto)->decrement('vacancy', 1);

//            DB::table('apartaments')->where('id',$id_apto)->update(['vacancy' => DB::raw('vacancy-1')]);;

//            if($decrement){
                return [
                    'fullName' => $faker->name,
                    'registration' => $faker->unique()->numberBetween(100000000,999999999),
                    'age' => rand(15,100),
                    'email' => $faker->unique()->safeEmail,
                    'password' => $password ?: $password = bcrypt('segredo123'),
                    'rg' => $faker->unique()->numberBetween(1000000000,9999999999),
                    'cpf' => $faker->numerify("###########"),
                    'genre' => $faker->randomElement(["M", "F"]),
                    'id_course' => random_int((int)DB::table('courses')->min('id'), (int)DB::table('courses')->max('id')),
                    'id_apto' => (int)$user->id,
                    'is_bse_active' => rand(0,1),
                    'is_admin' => rand(0,1),
                    'remember_token' => str_random(10),
                ];
//            }

        }else {
            return [
                'fullName' => $faker->name,
                'registration' => $faker->unique()->numberBetween(100000000,999999999),
                'age' => rand(15,100),
                'email' => $faker->unique()->safeEmail,
                'password' => $password ?: $password = bcrypt('segredo123'),
                'rg' => $faker->unique()->numberBetween(1000000000,9999999999),
                'cpf' => $faker->numerify("###########"),
                'genre' => $faker->randomElement(["M", "F"]),
                'id_course' => random_int((int)DB::table('courses')->min('id'), (int)DB::table('courses')->max('id')),
                'id_apto' => null,
                'is_bse_active' => rand(0,1),
                'is_admin' => rand(0,1),
                'remember_token' => str_random(10),
            ];
        }
    }



});
