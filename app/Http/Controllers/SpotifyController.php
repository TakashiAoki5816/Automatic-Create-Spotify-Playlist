<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthorizeException;
use App\Http\Requests\AccessTokenRequest;
use App\Http\Requests\AuthorizeRequest;
use App\Http\Requests\CreatePlaylistRequest;
use App\Http\Services\GuzzleService;
use App\Http\Services\SpotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

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
        }

        return redirect($authorizeUrl);
    }

    /**
     * Access Tokenを取得し、セッションに格納
     *
     * @param AccessTokenRequest $request
     * @throws CanNotGetAccessTokenException $e
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
        }

        return redirect()->route('main.index');
    }

    /**
     * ユーザー自身のプレイリストを取得する
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function retrieveMyPlaylist(Request $request): JsonResponse
    {
        $accessToken = $request->session()->get('access_token');
        $playlists = $this->spotifyService->retrieveMyPlayList($accessToken);

        return response()->json($playlists);
    }

    /**
     * 対象のプレイリストから指定したジャンルだけを抽出したプレイリストを作成
     *
     * @param CreatePlaylistRequest $request
     * @return
     */
    public function createPlayList(CreatePlaylistRequest $request)
    {
        $validated = $request->validated();
        $accessToken = $request->session()->get('access_token');

        // 新規 空プレイリスト作成
        $response = $this->spotifyService->createNewPlayList($accessToken, $validated['playlist_name']);
        $content = json_decode($response->getBody());

        // 指定プレイリスト内にある全ての楽曲IDを取得
        $trackIds = $this->spotifyService->retrieveTargetPlaylistAllTrackIds($accessToken, $validated['target_playlist_ids']);

        // ArtistDataからじゃないとジャンルを取得することができない
        // genresから何もJ-POP, K-POP, 洋楽とするか定める必要があるgenresテーブルを作成する　 etc. ONE OK ROCK j-pop, j-poprock, j-rock    Blueno Mars pop, dance pop    BTS k-pop, k-pop boy group
        // $response3 = $this->spotifyService->fetchArtistData($accessToken);

        $response = $this->spotifyService->addTracksToNewPlaylist($accessToken, $content->id, $trackIds);
        return response()->json($response);
    }
}
