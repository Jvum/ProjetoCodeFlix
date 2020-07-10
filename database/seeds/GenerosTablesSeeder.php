<?php

use Illuminate\Database\Seeder;

class GenerosTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Genero::class, 100)->create();

        // $this->call(UsersTableSeeder::class);
    }
}
