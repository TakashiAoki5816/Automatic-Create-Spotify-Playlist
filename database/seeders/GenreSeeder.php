<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Genre::truncate();
        $viewOrder = 1;

        Genre::insert([
            ['name' => 'j-pop', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'j-acoustic', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'japanese singer-songwriter', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'classic j-pop', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'j-rock', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'j-pop boy group', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'j-poprock', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'japanese emo', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'japanese punk rock', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'japanese teen pop', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'j-idol', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'eurobeat', 'genre_category_id' => 1, 'view_order' => $viewOrder++],
            ['name' => 'korean pop', 'genre_category_id' => 2, 'view_order' => $viewOrder++],
            ['name' => 'dance pop', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'boy band', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'pop', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'classic rock', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'folk', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'folk rock', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'melancholia', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'mellow gold', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'soft rock', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'uk pop', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'uk dance', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'uk funky', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'permanent wave', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'viral pop', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'hip hop', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'pittsburgh rap', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'pop rap', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'rap', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'post-teen pop', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'tropical house', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'indietronica', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'modern rock', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'emo', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'pop punk', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'neo mellow', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'sertanejo', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'sertanejo universitario', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'movie tunes', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'tagalog worship', 'genre_category_id' => 3, 'view_order' => $viewOrder++],
            ['name' => 'complextro', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'german techno', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'canadian pop', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'pop dance', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'electropop', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'brostep', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'edm', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'progressive electro house', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'deep pop edm', 'genre_category_id' => 4, 'view_order' => $viewOrder++],
            ['name' => 'anime', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'anime rock', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'seinen', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'shonen', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'shojo', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'oshare kei', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'visual kei', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'denpa-kei', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'anime score', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'shibuya-kei', 'genre_category_id' => 5, 'view_order' => $viewOrder++],
            ['name' => 'vocaloid', 'genre_category_id' => 6, 'view_order' => $viewOrder++],
            ['name' => 'j-pixie', 'genre_category_id' => 6, 'view_order' => $viewOrder++],
            ['name' => 'rhythm game', 'genre_category_id' => 6, 'view_order' => $viewOrder++],
            ['name' => 'japanese vtuber', 'genre_category_id' => 7, 'view_order' => $viewOrder++],
        ]);
    }
}
