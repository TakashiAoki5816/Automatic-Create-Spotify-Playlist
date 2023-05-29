<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthorizeException;
use App\Http\Requests\AccessTokenRequest;
use App\Http\Requests\AuthorizeRequest;
use App\Http\Services\GuzzleService;
use App\Http\Services\SpotifyService;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Spotify;

class SpotifyController extends Controller
{
    protected $spotifyService;
    protected $guzzleService;
    protected $guzzleClient;

    /**
     * SpotifyController Constructor
     *
     * @param Spotify $spotify
     * @param GuzzleService $guzzleService
     * @param Client $client
     */
    public function __construct(
        SpotifyService $spotifyService,
        GuzzleService $guzzleService,
        Client $client,
    ) {
        $this->spotifyService = $spotifyService;
        $this->guzzleService = $guzzleService;
        $this->guzzleClient = $client;
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
    public function getAccessToken(AccessTokenRequest $request): RedirectResponse
    {
        try {
            $accessTokenEntity = $request->toEntity();
            list($url, $body) = $accessTokenEntity->retrieveRequestItems();

            $response = $this->guzzleService->requestWithBody($url, $body);
            $request->storeAccessTokenToSession(json_decode($response->getBody()));
        } catch (CanNotGetAccessTokenException $e) {
            throw $e;
        }

        return redirect()->route('main.index');
    }

    /**
     * 自身のプレイリストを取得する
     *
     * @param Request $request
     * @return void
     */
    public function retrieveMyPlaylist(Request $request): JsonResponse
    {
        $accessToken = $request->session()->get('access_token');
        $playlists = $this->spotifyService->retrieveMyPlayList($accessToken);

        return response()->json($playlists);
    }

    /**
     * オリジナルのプレイリストを作成
     *
     * @param Request $request
     * @return
     */
    public function createPlayList(Request $request)
    {
        // $playlistName = $request->input('playlist_name');
        $accessToken = $request->session()->get('access_token');
        // $this->spotifyService->createPlayList($accessToken);
        $this->spotifyService->retrieveTargetPlaylistItems($accessToken, $request->input('target_playlist_ids'));
        $this->spotifyService->retrieveCurrentPlayList($accessToken);
        // 作成されたプレイリストのIDを取得
        // プレイリストから曲を取得
        $response = $this->spotifyService->fetchItemsFromPlaylist($accessToken);
        // ArtistDataからじゃないとジャンルを取得することができない
        // genresから何もJ-POP, K-POP, 洋楽とするか定める必要があるgenresテーブルを作成する　 etc. ONE OK ROCK j-pop, j-poprock, j-rock    Blueno Mars pop, dance pop    BTS k-pop, k-pop boy group
        // $response3 = $this->spotifyService->fetchArtistData($accessToken);

        $content = json_decode($response->getBody());
        $trackIds = $this->spotifyService->retrieveTrackIds($content->items);
        // $response = $this->spotifyService->addItemToPlaylist($accessToken, $trackIds);
        return response()->json($response);
    }
}
