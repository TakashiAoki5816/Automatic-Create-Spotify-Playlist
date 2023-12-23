<?php

namespace Database\Seeders;

use Database\Seeders\GenreSeeder;
use Database\Seeders\GenreCategorySeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            GenreSeeder::class,
            GenreCategorySeeder::class,
        ]);
    }
}
