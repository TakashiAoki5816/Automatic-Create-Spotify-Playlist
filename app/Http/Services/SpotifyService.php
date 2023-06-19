<?php

namespace App\Http\Services;

use App\Http\Services\GuzzleService;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use stdClass;

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
     * 新規 空プレイリスト作成
     *
     * @param string $accessToken
     * @param string $playlistName フォームから送られてきたプレイリスト名
     * @return GuzzleResponse
     */
    public function createNewPlayList(string $accessToken, string $playlistName): GuzzleResponse
    {
        // ユーザーID取得
        $userId = $this->retrieveUserId($accessToken);

        $formData = [
            "name" => $playlistName,
            "public" => true, // TODO ゆくゆくはpublic, private選べるようにする
        ];

        return $this->guzzleService->requestToSpotify($accessToken, "POST", "/users/{$userId}/playlists", $formData);
    }

    /**
     * ユーザーID 取得
     *
     * @param string $accessToken
     * @return string ユーザーID
     */
    public function retrieveUserId(string $accessToken): string
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/me");
        $content = $this->toDecodeJson($response);

        return $content->id;
    }

    public function toDecodeJson($response)
    {
        return json_decode($response->getBody()->getContents());
    }

    public function retrieveMyPlayList(string $accessToken)
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/me/playlists");
        return $this->toDecodeJson($response);
    }

    /**
     * 指定プレイリスト内にある全ての楽曲ID取得
     *
     * @param string $accessToken
     * @param array $targetPlaylistIds
     * @return array<string> $trackIds 指定プレイリスト内にある全ての楽曲ID
     */
    public function retrieveTargetPlaylistAllTrackIds(string $accessToken, array $targetPlaylistIds): array
    {
        return collect($targetPlaylistIds)->map(function (string $playlistId) use ($accessToken) {
            // １回目のリクエスト （一度のリクエストで取得できる楽曲は100曲まで）
            $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/playlists/{$playlistId}");
            $playlistData = $this->toDecodeJson($response);

            // 1回目のリクエストで取得した最大100曲分の楽曲IDを格納
            $playlistTrackIds = collect($playlistData->tracks->items)->map(function (stdClass $item) {
                return $item->track->id;
            })->toArray();

            // 必要なリクエスト回数 = 全アイテム数 ÷ 100(一度のリクエストで取得できる最大アイテム数) 切り上げ
            $count = ceil($playlistData->tracks->total / self::$maxItemsPerRequest);

            // ２回目以降の必要なリクエスト回数分ループ
            for ($i = 2; $i <= $count; $i++) {
                $url = $i == 2 ? $playlistData->tracks->next : $playlistData->next;

                $response = $this->guzzleService->requestByUrl($accessToken, 'GET', $url);
                $playlistData = $this->toDecodeJson($response);
                $trackIds = collect($playlistData->items)->map(function (stdClass $item) {
                    return $item->track->id;
                })->toArray();

                $playlistTrackIds = array_merge($playlistTrackIds, $trackIds);
            }

            return $playlistTrackIds;
        })->flatten()->toArray();
    }

    public function fetchArtistData(string $accessToken): GuzzleResponse
    {
        return $this->guzzleService->requestToSpotify($accessToken, "GET", "/artists/3Nrfpe0tUJi4K4DXYWgMUX");
    }

    /**
     * 作成したプレイリストに楽曲を追加
     *
     * @param string $accessToken
     * @param string $playlistId 作成したプレイリストID
     * @param array $trackIds 追加する楽曲IDs
     * @return GuzzleResponse
     */
    public function addTracksToNewPlaylist(string $accessToken, string $playlistId, $trackIds): GuzzleResponse
    {
        $trackUrisArr = collect($trackIds)->map(function (string $trackId) {
            return 'spotify:track:' . $trackId;
        })->toArray();

        $formData = [
            "uris" => $trackUrisArr,
            "position" => 0,
        ];

        // 一度のリクエストで最大100曲まで追加できる
        // TODO：何回りクエストする必要があるのかを計算
        return $this->guzzleService->requestToSpotify($accessToken, "POST", "/playlists/{$playlistId}/tracks", $formData);
    }
}
