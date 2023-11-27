<?php

namespace App\Http\Services;

use App\Http\Repositories\GenreCategoryRepository;
use App\Http\Repositories\GenreRepository;
use App\Http\Services\GuzzleService;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use stdClass;

class SpotifyService
{
    protected $guzzleService;
    protected $genreRepository;
    protected $genreCategoryRepository;
    private static $maxItemsPerRequest = 100; // 一度のリクエストで取得できる最大アイテム数

    /**
     * SpotifyService Constructor
     *
     * @param GuzzleService $guzzleService
     */
    public function __construct(
        GuzzleService $guzzleService,
        GenreRepository $genreRepository,
        GenreCategoryRepository $genreCategoryRepository,
    ) {
        $this->guzzleService = $guzzleService;
        $this->genreRepository = $genreRepository;
        $this->genreCategoryRepository = $genreCategoryRepository;
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

    /**
     * JSON文字列のレスポンスボディを取得し、デコード化
     *
     * @param GuzzleResponse $response
     * @return stdClass
     */
    public function toDecodeJson(GuzzleResponse $response): stdClass
    {
        return json_decode($response->getBody()->getContents());
    }

    /**
     * ユーザー自身のプレイリストを取得する
     *
     * @param string $accessToken
     * @return stdClass
     */
    public function retrieveMyPlayList(string $accessToken): stdClass
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/me/playlists");
        return $this->toDecodeJson($response);
    }

    /**
     * 指定プレイリスト内にある全てのトラックID取得
     *
     * @param string $accessToken
     * @param array $targetPlaylistIds 対象プレイリストIDs
     * @return Collection<string $trackId, string $artistId> 対象プレイリスト内にある全てのトラックID/アーティストIDを単一の連想配列に格納したコレクション
     */
    public function retrieveAllTrackIdAndArtistIdByTargetPlaylist(string $accessToken, array $targetPlaylistIds): Collection
    {
        // プレイリストごとにトラックID, アーティストIDを格納したコレクション
        $allTrackIdAndArtistIdsCollection = $this->retrieveAllTrackIdAndArtistIds($accessToken, $targetPlaylistIds);

        // 複数の連想配列から単一の連想配列に集約
        return $allTrackIdAndArtistIdsCollection->reduce(function ($carry, $item) {
            return $carry->mergeRecursive($item);
        }, collect());
    }

    public function retrieveAllTrackIdAndArtistIds(string $accessToken, array $targetPlaylistIds): Collection
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
     * 選択されたジャンルでフィルタリングしたコレクションを返却
     *
     * @param string $accessToken
     * @param string $selectedGenre 選択されたジャンル
     * @param Collection $allTrackIdAndArtistIdCollection
     * @return Collection $filteredTrackIdAndArtistIdCollection フィルタリングされたトラックID・アーティストIDコレクション
     */
    public function filteredSelectedGenre(string $accessToken, string $selectedGenre, Collection $allTrackIdAndArtistIdCollection): Collection
    {
        $filteredTrackIdAndArtistIdCollection = $allTrackIdAndArtistIdCollection->filter(function (array $trackIdAndAccessTokenArray) use ($accessToken, $selectedGenre) {
            // アーティストに設定されているジャンルを取得
            $artistGenres = $this->fetchGenresByArtistId($accessToken, $trackIdAndAccessTokenArray['artist_id']);

            // genresがない場合の制御
            // genresはあるけど、想定外のジャンル
            // 選択されたジャンルなのかをフィルタリング
            $containFlg = $this->checkContainSelectedGenre($artistGenres, $selectedGenre, $trackIdAndAccessTokenArray['track_id']);
            if ($containFlg) {
                return true;
            }

            return false;
        });

        return $filteredTrackIdAndArtistIdCollection;
    }

    /**
     * アーティストIDからジャンルを取得
     *
     * @param string $accessToken
     * @param string $artistId
     * @return array アーティストに設定されているジャンル
     */
    public function fetchGenresByArtistId(string $accessToken, string $artistId): array
    {
        $response = $this->guzzleService->requestToSpotify($accessToken, "GET", "/artists/{$artistId}");
        $content = $this->toDecodeJson($response);

        return $content->genres;
    }

    /**
     * 選択されたジャンルが含まれているか確認
     *
     * @param string $artistGenres アーティストに設定されているジャンル
     * @param string $selectedGenre 選択されたジャンル
     * @return bool $containFlg 選択されたジャンルが含まれているかのフラグ trueの場合、含まれている
     */
    public function checkContainSelectedGenre(array $artistGenres, string $selectedGenre): bool
    {
        $containFlg = false;

        // アーティストに設定されているジャンルが選択されたジャンルに含まれているか
        collect($artistGenres)->each(function (string $artistGenre) use ($selectedGenre, &$containFlg) {
            // アーティストジャンルから、どのジャンルカテゴリーなのかを取得
            $genreCategoryName = $this->fetchGenreCategoryNameByArtistGenre($artistGenre);

            // 取得したジャンルが選択されたジャンルなのかを比較、一致した場合eachを抜けcontainFlgをtrueに変換
            if ($genreCategoryName === $selectedGenre) {
                $containFlg = true;
                // eachメソッドを抜ける
                return false;
            }
        });

        return $containFlg;
    }

    /**
     * アーティストジャンルからジャンルカテゴリー名を取得
     *
     * @param string $artistGenre アーティストジャンル
     * @return string ジャンルカテゴリー名
     */
    public function fetchGenreCategoryNameByArtistGenre(string $artistGenre): ?string
    {
        $genre = $this->genreRepository->findGenreByName($artistGenre);
        if (is_null($genre)) {
            Log::debug("登録されていないジャンルです:{$artistGenre}");
            return null;
        }

        $genreCategory = $this->genreCategoryRepository->findGenreCategoryByGenreCategoryId($genre->genre_category_id);

        return $genreCategory->name;
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
