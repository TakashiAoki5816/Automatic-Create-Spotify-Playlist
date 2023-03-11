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
     * プレイリスト作成
     *
     * @return GuzzleResponse
     */
    public function createPlayList($accessToken): GuzzleResponse
    {
        $userId = $this->retrieveUserId($accessToken);
        $formData = [
            "name" => "Laravelテストプレイリストdemo作成",
            "description" => "Laravelテストプレイリスト description",
            "public" => true,
        ];

        return $this->guzzleService->requestToSpotify($accessToken, "POST", "/users/{$userId}/playlists", $formData);
    }

    public function toDecodeJson($response)
    {
        return json_decode($response->getBody()->getContents());
    }

    public function retrieveUserId(string $accessToken): string
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/me");
        $content = $this->toDecodeJson($response);

        return $content->id;
    }

    public function retrieveCurrentPlayLists(string $accessToken)
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/me/playlists");
        return $this->toDecodeJson($response);
    }

    public function retrievePlaylistId($accessToken, $playlistName)
    {
    }

    public function fetchItemsFromPlaylist(string $accessToken): GuzzleResponse
    {
        return $this->guzzleService->requestToSpotify($accessToken, "GET", "/playlists/1lCObPysmM50HzRZcpErJv/tracks?offset=100");
    }

    public function fetchArtistData(string $accessToken): GuzzleResponse
    {
        return $this->guzzleService->requestToSpotify($accessToken, "GET", "/artists/3Nrfpe0tUJi4K4DXYWgMUX");
    }

    public function retrieveTrackIds($tracks)
    {
        return collect($tracks)->map(function ($trackInfo) {
            return $trackInfo->track->id;
        })->toArray();
    }

    public function addItemToPlaylist(string $accessToken, $trackIds): GuzzleResponse
    {
        $trackUrisArr = collect($trackIds)->map(function ($trackId) {
            return 'spotify:track:' . $trackId;
        })->toArray();

        $formData = [
            "uris" => $trackUrisArr,
            "position" => 0,
        ];
        return $this->guzzleService->requestToSpotify($accessToken, "POST", "/playlists/5Lon8tamI9cexEHouArVIM/tracks", $formData);
    }
}
