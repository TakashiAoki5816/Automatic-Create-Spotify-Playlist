<?php

namespace App\Http\Services;

use App\Http\Services\GuzzleService;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Collection;
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
     * 指定プレイリスト内にある全てのトラックID取得
     *
     * @param string $accessToken
     * @param array $targetPlaylistIds
     * @return array<string> $trackIds 指定プレイリスト内にある全てのトラックID
     */
    public function retrieveAllTrackIdAndArtistIdByTargetPlaylist(string $accessToken, array $targetPlaylistIds): array
    {
        $aaa = collect([]);
        $allPlaylistTrackIdAndArtistIdsCollection = collect($targetPlaylistIds)->map(function (string $playlistId) use ($accessToken) {
            // １回目のリクエスト （一度のリクエストで取得できるトラックは100曲まで）
            $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/playlists/{$playlistId}");
            $playlistData = $this->toDecodeJson($response);

            // 1回目のリクエストで取得した最大100曲分のトラックID/アーティストIDを格納
            $firstPlaylistTrackIdAndArtistIdsCollection = collect($playlistData->tracks->items)->map(function (stdClass $item) {
                return [
                    'track_id' => $item->track->id,
                    'artist_id' => $item->track->artists[0]->id,
                ];
            });

            $playlistTrackIdAndArtistIdsCollection = $firstPlaylistTrackIdAndArtistIdsCollection;

            // 必要なリクエスト回数 = 全アイテム数 ÷ 100(一度のリクエストで取得できる最大アイテム数) 切り上げ
            $count = ceil($playlistData->tracks->total / self::$maxItemsPerRequest);

            // ２回目以降の必要なリクエスト回数分ループ
            for ($i = 2; $i <= $count; $i++) {
                $url = ($i == 2) ? $playlistData->tracks->next : $playlistData->next;

                $response = $this->guzzleService->requestByUrl($accessToken, 'GET', $url);
                $playlistData = $this->toDecodeJson($response);
                $trackIdAndPlaylistIdsCollection = collect($playlistData->items)->map(function (stdClass $item) {
                    return [
                        'track_id' => $item->track->id,
                        'artist_id' => $item->track->artists[0]->id,
                    ];
                });

                $playlistTrackIdAndArtistIdsCollection = $playlistTrackIdAndArtistIdsCollection->merge($trackIdAndPlaylistIdsCollection);
            }

            return $playlistTrackIdAndArtistIdsCollection;
        });

        return $allPlaylistTrackIdAndArtistIdsCollection->reduce(function ($carry, $item) {
            return $carry->mergeRecursive($item);
        }, collect());
    }

    public function fetchArtistData(string $accessToken, array $trackIds)
    {
        // TODO どこかで使う　flatten()
        // return $this->guzzleService->requestToSpotify($accessToken, "GET", "/artists/3Nrfpe0tUJi4K4DXYWgMUX");
    }

    /**
     * 作成したプレイリストにトラックを追加
     *
     * @param string $accessToken
     * @param string $playlistId 作成したプレイリストID
     * @param array $trackIds 追加するトラックIDs
     * @return GuzzleResponse
     */
    public function addTracksToNewPlaylist(string $accessToken, string $playlistId, array $trackIds)
    {
        // トラックURI形式にトラックIDを整形し、100曲単位に分割する(一度のリクエストで追加できるのは最大100曲までのため)
        $trackUrisChunksCollection = collect($trackIds)->map(function (string $trackId) {
            return 'spotify:track:' . $trackId;
        })->chunk(100);

        // 100曲ずつトラックを追加する
        $trackUrisChunksCollection->each(function (Collection $trackUris) use ($accessToken, $playlistId) {
            $formData = [
                "uris" => $trackUris->values(), // valuesでindexを0から振り直し、0からじゃないとリクエスト失敗する
            ];

            $this->guzzleService->requestToSpotify($accessToken, "POST", "/playlists/{$playlistId}/tracks", $formData);
        });
    }
}
