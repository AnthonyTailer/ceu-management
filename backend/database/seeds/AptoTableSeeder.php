<?php

use Illuminate\Database\Seeder;

class AptoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Apartament::class, 50)->create();
    }
}
