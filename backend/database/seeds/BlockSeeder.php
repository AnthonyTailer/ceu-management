<?php

use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Block::class, 30)->create()->each(function ($u) {
            $u->blocks()->save(factory(App\Block::class)->make());
        });
    }
}
