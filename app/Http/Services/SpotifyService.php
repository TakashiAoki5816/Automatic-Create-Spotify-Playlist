<?php

namespace App\Http\Services;

use Spotify;
use GuzzleHttp\Client;

class SpotifyService
{
    /**
     * SpotifyService Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * 認可したいURLを取得
     *
     * @return string
     */
    public function getAuthorizeUrl(): string
    {
        $baseUrl = 'https://accounts.spotify.com/authorize';
        return $baseUrl . '?client_id=' . env('SPOTIFY_CLIENT_ID') . '&response_type=code' . '&redirect_uri=http://localhost' . '&scope=user-top-read';
    }
}
