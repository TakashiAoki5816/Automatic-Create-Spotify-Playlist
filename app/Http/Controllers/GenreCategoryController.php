<?php

namespace App\Http\Controllers;

use App\Http\Services\GenreCategoryService;
use Illuminate\Http\Request;

class GenreCategoryController extends Controller
{
    /**
     * @var $genreCategoryService
     */
    private $genreCategoryService;

    /**
     * GenreCategoryController Constructor
     *
     * @param GenreCategoryService $genreCategoryService
     */
    public function __construct(
        GenreCategoryService $genreCategoryService,
    ) {
        $this->genreCategoryService = $genreCategoryService;
    }

    public function genreCategories()
    {
        return $this->genreCategoryService->genreCategories();
    }
}
