<?php

namespace App\Http\Services;

use Spotify;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class SpotifyService
{
    protected $client;
    /**
     * SpotifyService Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getAccessTokenRequest($code)
    {
        $url = 'https://accounts.spotify.com/api/token';
        $params = [
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'http://127.0.0.1:8000'
        ];
        $response = Http::asForm()->post($url, $params);

        return $response;
    }

    /**
     * 認可したいURLを取得
     *
     * @return string
     */
    public function getAuthorizeUrl(): string
    {
        $baseUrl = 'https://accounts.spotify.com/authorize';
        return $baseUrl . '?client_id=' . env('SPOTIFY_CLIENT_ID') . '&response_type=code' . '&redirect_uri=http://127.0.0.1:8000' . '&scope=user-top-read';
    }
}
