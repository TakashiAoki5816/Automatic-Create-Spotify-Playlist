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

    /**
     * 指定のプレイリストから全ての楽曲取得
     *
     * @param string $accessToken
     * @param array $targetPlaylistIds
     * @return void
     */
    public function retrieveTargetPlaylistAllTracks(string $accessToken, array $targetPlaylistIds)
    {
        $allTrackIds = collect($targetPlaylistIds)->map(function (string $playlistId) use ($accessToken) {
            // １回目のリクエスト （一度のリクエストで取得できる楽曲は100曲まで）
            $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/playlists/{$playlistId}");
            $playlistData = $this->toDecodeJson($response);

            // ループ回数 = 全アイテム数 ÷ 100(一度のリクエストで取得できる最大アイテム数)
            $loopCount = ceil($playlistData->tracks->total / self::$maxItemsPerRequest);

            // プレイリスト内にあるアイテムのループ（100曲分）
            $playlistTrackIds = collect($playlistData->tracks->items)->map(function ($item) {
                // 大元にトラックIDを追加
                return $item->track->id;
            })->toArray();

            // ２回目以降の必要なリクエスト回数分ループ
            for ($i = 2; $i <= $loopCount; $i++) {
                // TODO ループさせる方法 nextまたはクエリパラメータで上手くループさせる方法を導き出す
                $response = $this->guzzleService->requestByUrl($accessToken, 'GET', $playlistData->tracks->next);
                $playlistData = $this->toDecodeJson($response);
                $trackIds = collect($playlistData->items)->each(function ($item) {
                    return $item->track->id;
                });

                array_push($playlistTrackIds, $trackIds);
            }

            return $playlistTrackIds;
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
