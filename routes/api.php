<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api'])->prefix('spotify')->name('spotify.')->group(function () {
    Route::get('/authorization', ['App\Http\Controllers\SpotifyController'::class, 'authorization'])->name('authorization');
    Route::get('/callback', ['App\Http\Controllers\SpotifyController'::class, 'accessToken'])->name('accessToken');
    Route::get('/myPlaylist', ['App\Http\Controllers\SpotifyController'::class, 'retrieveMyPlaylist'])->name('retrieveMyPlaylist');
    Route::post('/createPlaylist', ['App\Http\Controllers\SpotifyController'::class, 'createPlaylist'])->name('createPlaylist');
});
