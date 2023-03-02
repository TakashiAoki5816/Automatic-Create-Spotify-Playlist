<?php

namespace App\Http\Services;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use App\Http\Services\GuzzleService;

class SpotifyService
{
    protected $guzzleService;
    /**
     * SpotifyService Constructor
     *
     * @param GuzzleService $guzzleService
     */
    public function __construct(GuzzleService $guzzleService)
    {
        $this->guzzleService = $guzzleService;
    }

    /**
     * プレイリストを作成
     *
     * @return GuzzleResponse
     */
    public function createPlayList($accessToken): GuzzleResponse
    {
        $userId = $this->getUserId($accessToken);
        $formData = [
            "name" => "Laravelテストプレイリストdemo作成",
            "description" => "Laravelテストプレイリスト description",
            "public" => true,
        ];

        return $this->guzzleService->requestToSpotify($accessToken, "POST", "/users/{$userId}/playlists", $formData);
    }

    public function getUserId(string $accessToken): string
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "me");

        $content = json_decode($response->getBody()->getContents());
        return $content->id;
    }

    public function fetchItemsFromPlaylist(string $accessToken): GuzzleResponse
    {
        return $this->guzzleService->requestToSpotify($accessToken, "GET", "/playlists/1lCObPysmM50HzRZcpErJv/tracks?offset=100");
    }

    public function fetchTrackDetails(string $accessToken): GuzzleResponse
    {
        return $this->guzzleService->requestToSpotify($accessToken, "GET", "/tracks/36p84XGX2lLHGudXzf3Krq");
    }

    public function fetchArtistData(string $accessToken): GuzzleResponse
    {
        return $this->guzzleService->requestToSpotify($accessToken, "GET", "/artists/3Nrfpe0tUJi4K4DXYWgMUX");
    }

    public function addItemToPlaylist(string $accessToken): GuzzleResponse
    {
        $formData = [
            "uris" => [
                "spotify:track:1yt4wO7dKCwsfjch8SN9aU"
            ],
            "position" => 0,
        ];
        return $this->guzzleService->requestToSpotify($accessToken, "POST", "/playlists/3OFS2fzeVGK1pfn9ujk4SS/tracks", $formData);
    }
}
