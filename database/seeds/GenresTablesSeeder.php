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
        $categories = \App\Models\Category::all();
        factory(\App\Models\Genero::class, 100)
            ->create()
            ->each(function (\App\Models\Genero $genero) use($categories) {
                $categoriesId = $categories->random(5)->pluck('id')->toArray();
                $genero->categories()->attach($categoriesId);
            });
        // $this->call(UsersTableSeeder::class);
    }
}
