<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthorizeException;
use App\Http\Requests\AccessTokenRequest;
use App\Http\Requests\AuthorizeRequest;
use App\Http\Requests\CreatePlaylistRequest;
use App\Http\Services\GuzzleService;
use App\Http\Services\SpotifyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;

class SpotifyController extends Controller
{
    /**
     * @var $spotifyService
     * @var $guzzleService
     */
    private $spotifyService;
    private $guzzleService;

    /**
     * SpotifyController Constructor
     *
     * @param SpotifyService $spotifyService
     * @param GuzzleService $guzzleService
     */
    public function __construct(
        SpotifyService $spotifyService,
        GuzzleService $guzzleService,
    ) {
        $this->spotifyService = $spotifyService;
        $this->guzzleService = $guzzleService;
    }

    /**
     * 認可したいURLを返却
     *
     * @param AuthorizeRequest $request
     * @throws AuthorizeException $e
     * @throws Exception $e
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function authorization(AuthorizeRequest $request): Redirector|RedirectResponse
    {
        try {
            $authorizeEntity = $request->toEntity();
            $authorizeUrl = $authorizeEntity->retrieveRequestUrl();
        } catch (AuthorizeException $e) {
            Log::error('authorization@SpotifyController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        } catch (Exception $e) {
            Log::error('authorization@SpotifyController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }

        return redirect($authorizeUrl);
    }

    /**
     * アクセストークンを取得し、セッションに格納
     *
     * @param AccessTokenRequest $request
     * @throws CanNotGetAccessTokenException $e
     * @throws Exception $e
     * @return RedirectResponse
     */
    public function accessToken(AccessTokenRequest $request): RedirectResponse
    {
        try {
            $accessTokenEntity = $request->toEntity();
            list($url, $body) = $accessTokenEntity->retrieveRequestItems();

            $response = $this->guzzleService->requestWithBody($url, $body);
            $request->storeAccessTokenToSession(json_decode($response->getBody()));
        } catch (CanNotGetAccessTokenException $e) {
            Log::error('accessToken@SpotifyController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        } catch (Exception $e) {
            Log::error('accessToken@SpotifyController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }

        return redirect()->route('main.index');
    }

    /**
     * ユーザー自身のプレイリストを取得する
     *
     * @param Request $request
     * @throws Exception $e
     * @return JsonResponse
     */
    public function retrieveMyPlaylist(Request $request): JsonResponse
    {
        try {
            $accessToken = $request->session()->get('access_token');
            $playlists = $this->spotifyService->retrieveMyPlayList($accessToken);
        } catch (Exception $e) {
            Log::error('retrieveMyPlaylist@SpotifyController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }

        return response()->json($playlists);
    }

    /**
     * 対象のプレイリストから指定したジャンルだけを抽出したプレイリストを作成
     *
     * @param CreatePlaylistRequest $request
     * @throws Exception $e
     * @return
     */
    public function createPlayList(CreatePlaylistRequest $request)
    {
        $validated = $request->validated();
        $accessToken = $request->session()->get('access_token');

        try {
            // 指定プレイリスト内にある全てのトラックID/アーティストIDを単一の連想配列に格納したコレクション
            $allTrackIdAndArtistIdCollection = $this->spotifyService->getAllTrackIdAndArtistIdByTargetPlaylist($accessToken, $validated['target_playlist_ids']);

            // TODO getAllTrackIdAndArtistIdByTargetPlaylist で取得したレスポンスの中でフィルタリングできそう
            // 責務としては今の処理がわかりやすいけど、無駄なリクエストが発生している
            $filteredTrackIdAndArtistIdCollection = $this->spotifyService->filteredSelectedGenre($accessToken, $validated['genres'], $allTrackIdAndArtistIdCollection);

            // 新規 空プレイリスト作成
            $response = $this->spotifyService->createNewPlayList($accessToken, $validated['playlist_name']);
            $content = json_decode($response->getBody());

            // 作成したプレイリストにトラック追加
            $this->spotifyService->addTracksToNewPlaylist($accessToken, $content->id, $filteredTrackIdAndArtistIdCollection);
        } catch (Exception $e) {
            Log::error('createPlayList@SpotifyController: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }

        return response()->json([
            'status' => 200,
        ]);
    }
}
