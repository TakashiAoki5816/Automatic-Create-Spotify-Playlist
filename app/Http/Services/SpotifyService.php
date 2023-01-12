<?php

namespace App\Http\Services;

use Spotify;
use GuzzleHttp\Client;
use Illuminate\Http\Client\Response;
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

    /**
     * Access Token取得リクエストを行う
     *
     * @param string $code
     * @return Response
     */
    public function getAccessTokenRequest(string $code): Response
    {
        $url = 'https://accounts.spotify.com/api/token';
        $params = [
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => 'http://localhost'
        ];
        return Http::asForm()->post($url, $params);
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
