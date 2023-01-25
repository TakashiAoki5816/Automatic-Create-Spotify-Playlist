<?php

use Illuminate\Http\Request;
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

Route::middleware(['api'])->prefix('spotify')->group(function () {
    Route::get('/authorize', ['App\Http\Controllers\LoginController'::class, 'authorizeUrl'])->name('authorizeUrl');
    Route::get('/getAccessToken', ['App\Http\Controllers\SpotifyController'::class, 'getAccessToken'])->name('getAccessToken');
    Route::get('/createPlaylist', ['App\Http\Controllers\SpotifyController'::class, 'createPlaylist'])->name('createPlaylist');
});
