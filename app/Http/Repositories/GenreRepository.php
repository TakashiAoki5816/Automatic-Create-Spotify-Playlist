<?php

namespace App\Http\Repositories;

use App\Models\Genre;

class GenreRepository
{
    /**
     * ジャンルカテゴリー 一覧
     *
     * @return void
     */
    public function genre()
    {
        return GenreCategory::all()->pluck(['name', 'view_order']);
    }

    /**
     * ジャンル名からモデルを取得
     *
     * @param string $name ジャンル名
     * @return Genre ジャンル
     */
    public function findGenreByName(string $name): ?Genre
    {
        return Genre::where(['name' => $name])->first();
    }
}
