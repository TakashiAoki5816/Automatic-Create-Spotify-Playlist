<?php

namespace App\Http\Repositories;

use App\Models\GenreCategory;

class GenreCategoryRepository
{
    protected $genreCategoryRepository;

    /**
     * ジャンルカテゴリー 一覧
     *
     * @return void
     */
    public function genreCategories()
    {
        return GenreCategory::all()->pluck(['name', 'view_order']);
    }

    /**
     * ジャンルカテゴリーIDからGenreCategoryモデルを取得
     *
     * @param int $genreCategoryId ジャンルカテゴリーID
     * @return GenreCategory ジャンルカテゴリー
     */
    public function findByGenreCategoryId(int $genreCategoryId): GenreCategory
    {
        return GenreCategory::find($genreCategoryId);
    }
}
