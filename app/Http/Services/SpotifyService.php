<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class SpotifyService
{
    protected $baseApiUrl = "https://api.spotify.com/v1/";

    protected $guzzleClient;
    /**
     * SpotifyService Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function sendRequest($accessToken, $method, $uri, $formData = null)
    {
        return $this->guzzleClient->request(
            $method,
            $this->baseApiUrl . $uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $formData,
            ],
        );
    }

    /**
     * プレイリストを作成
     *
     * @return Response
     */
    public function createPlayList($accessToken): Response
    {
        $userId = $this->getUserId($accessToken);
        $formData = [
            "name" => "Laravelテストプレイリストdemo作成",
            "description" => "Laravelテストプレイリスト description",
            "public" => true,
        ];

        return $this->sendRequest($accessToken, "POST", "users/{$userId}/playlists", $formData);
    }

    public function getUserId(string $accessToken): string
    {
        $response = $this->sendRequest($accessToken, "GET", "me");

        $content = json_decode($response->getBody()->getContents());
        return $content->id;
    }

    public function fetchItemsFromPlaylist(string $accessToken)
    {
        return $this->sendRequest($accessToken, "GET", "playlists/1lCObPysmM50HzRZcpErJv/tracks");
    }

    public function fetchTrackDetails(string $accessToken)
    {
        return $this->sendRequest($accessToken, "GET", "tracks/36p84XGX2lLHGudXzf3Krq");
    }

    public function fetchArtistData(string $accessToken)
    {
        return $this->sendRequest($accessToken, "GET", "artists/3Nrfpe0tUJi4K4DXYWgMUX");
    }

    public function addItemToPlaylist(string $accessToken)
    {
        $formData = [
            "uris" => [
                "spotify:track:1yt4wO7dKCwsfjch8SN9aU"
            ],
            "position" => 0,
        ];
        return $this->sendRequest($accessToken, "POST", "playlists/3OFS2fzeVGK1pfn9ujk4SS/tracks", $formData);
    }
}
