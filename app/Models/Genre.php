<?php

namespace App\Models;

use App\Models\GenreCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'genre_category_id',
        'view_order',
    ];

    public function genreCategory()
    {
        return $this->hasOne(GenreCategory::class, 'id', 'genre_category_id');
    }
}
