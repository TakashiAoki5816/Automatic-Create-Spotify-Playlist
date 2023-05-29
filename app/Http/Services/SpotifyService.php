<?php

namespace App\Http\Services;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use App\Http\Services\GuzzleService;

class SpotifyService
{
    protected $guzzleService;
    private static $maxItemsPerRequest = 100; // 一度のリクエストで取得できる最大アイテム数

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

    public function retrieveMyPlayList(string $accessToken)
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/me/playlists");
        return $this->toDecodeJson($response);
    }

    public function retrieveTargetPlaylistItems(string $accessToken, array $targetPlaylistIds)
    {
        $addAllItems = [];
        // 対象プレイリストのループ
        collect($targetPlaylistIds)->map(function (string $playlistId) use ($accessToken) {
            // １回目のリクエスト
            $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/playlists/{$playlistId}");
            $playlistData = $this->toDecodeJson($response);

            // ループ回数　 = 全アイテム数　÷　一度のリクエストで取得できる最大アイテム数 (余りは切り上げ)
            $loopCount = ceil($playlistData->tracks->total / self::$maxItemsPerRequest);

            // プレイリスト内にあるアイテムのループ（100曲分）
            $trackIds = collect($playlistData->tracks->items)->map(function ($item) {
                // 大元にトラックIDを追加
                return $item->track->id;
            })->toArray();

            // ２回目以降の必要なリクエスト回数分ループ
            for ($i = 2; $i <= $loopCount; $i++) {
                $response = $this->guzzleService->requestByUrl($accessToken, 'GET', $playlistData->tracks->next);
                $playlistData = $this->toDecodeJson($response);
                collect($playlistData->tracks->items)->each(function ($item) use ($trackIds) {
                    $trackIds += $item->track->id;
                });
            }
        });
    }

    public function retrievePlaylistById($accessToken, $playlistId)
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
