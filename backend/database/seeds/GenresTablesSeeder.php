<?php

use Illuminate\Database\Seeder;

class GenreTableSeeder extends Seeder
{
    public function run()
    {
        $categories = \App\Models\Category::all();
        factory(\App\Models\Genre::class, 100)
            ->create()
            ->each(function (\App\Models\Genre $genero) use($categories) {
                $categoriesId = $categories->random(5)->pluck('id')->toArray();
                $genero->categories()->attach($categoriesId);
            });
        // $this->call(UsersTableSeeder::class);
    }
}
