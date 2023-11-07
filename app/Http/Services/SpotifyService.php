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
     * @return Collection<string $trackId, string $artistId> $trackIds 指定プレイリスト内にある全てのトラックID/アーティストID
     */
    public function getAllTrackIdAndArtistIdByTargetPlaylist(string $accessToken, array $targetPlaylistIds): Collection
    {
        // プレイリストごとにトラックID, アーティストIDを格納したコレクション
        $allTrackIdAndArtistIdsCollection = $this->getAllTrackIdAndArtistIds($accessToken, $targetPlaylistIds);

        // 複数の連想配列から単一の連想配列に集約
        return $allTrackIdAndArtistIdsCollection->reduce(function ($carry, $item) {
            return $carry->mergeRecursive($item);
        }, collect());
    }

    public function getAllTrackIdAndArtistIds(string $accessToken, array $targetPlaylistIds): Collection
    {
        return collect($targetPlaylistIds)->map(function (string $playlistId) use ($accessToken) {
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

                // 大元のコレクションに取得したデータをマージ
                $playlistTrackIdAndArtistIdsCollection = $playlistTrackIdAndArtistIdsCollection->merge($trackIdAndPlaylistIdsCollection);
            }

            return $playlistTrackIdAndArtistIdsCollection;
        });
    }

    /**
     * 指定されたジャンルで絞り込む
     *
     * @param string $accessToken
     * @param array $targetGenres 指定されたジャンル
     * @param Collection $allTrackIdAndArtistIdCollection
     * @return void
     */
    public function filteredTargetGenres(string $accessToken, array $targetGenres, Collection $allTrackIdAndArtistIdCollection)
    {
        $allTrackIdAndArtistIdCollection->filter(function (array $trackIdAndAccessTokenArray) use ($accessToken) {
            $genres = $this->fetchGenresByArtistId($accessToken, $trackIdAndAccessTokenArray['artist_id']);

            // genresがない場合の制御
            // genresはあるけど、想定外のジャンル
        });
    }

    /**
     * アーティストIDからジャンルを取得
     *
     * @param string $accessToken
     * @param string $artistId
     * @return array
     */
    public function fetchGenresByArtistId(string $accessToken, string $artistId): array
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/artists/{$artistId}");
        $content = $this->toDecodeJson($response);

        return $content->genres;
    }

    /**
     * 作成したプレイリストにトラックを追加
     *
     * @param string $accessToken
     * @param string $playlistId 作成したプレイリストID
     * @param Collection $allTrackIdAndArtistIdCollection 追加するトラックIDs
     * @return GuzzleResponse
     */
    public function addTracksToNewPlaylist(string $accessToken, string $playlistId, Collection $allTrackIdAndArtistIdCollection)
    {
        // トラックURI形式にトラックIDを整形し、100曲単位に分割する(一度のリクエストで追加できるのは最大100曲までのため)
        $trackUrisChunksCollection = $allTrackIdAndArtistIdCollection->map(function (array $trackIdAndArtistIdArray) {
            return 'spotify:track:' . $trackIdAndArtistIdArray['track_id'];
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
