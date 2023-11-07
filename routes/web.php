<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware(['web'])->group(function () {
    Route::name('main.')->group(function () {
        Route::get('/', ['App\Http\Controllers\HomeController'::class, 'index'])->name('index')->middleware('access_token');
    });
});
