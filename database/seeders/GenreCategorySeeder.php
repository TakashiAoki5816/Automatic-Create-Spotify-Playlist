<?php

namespace Database\Seeders;

use App\Models\GenreCategory;
use Illuminate\Database\Seeder;

class GenreCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GenreCategory::truncate();
        $viewOrder = 1;

        GenreCategory::insert([
            ['name' => 'J-POP', 'view_order' => $viewOrder++],
            ['name' => 'K-POP', 'view_order' => $viewOrder++],
            ['name' => '洋楽', 'view_order' => $viewOrder++],
            ['name' => 'EDM', 'view_order' => $viewOrder++],
            ['name' => 'アニソン', 'view_order' => $viewOrder++],
            ['name' => 'ボカロ', 'view_order' => $viewOrder++],
            ['name' => 'VTuber', 'view_order' => $viewOrder++],
        ]);
    }
}
