<?php

namespace App\Http\Controllers;

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
     * @throws $e
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function authorization(AuthorizeRequest $request): Redirector|RedirectResponse
    {
        try {
            $authorizeEntity = $request->toEntity();
            $url = $authorizeEntity->retrieveRequestUrl();
        } catch (\Exception $e) {
            throw $e;
        }

        return redirect($url);
    }

    /**
     * Access Tokenを取得し、セッションに格納
     *
     * @param AccessTokenRequest $request
     * @return JsonResponse
     */
    public function getAccessToken(AccessTokenRequest $request)
    {
        try {
            $accessTokenEntity = $request->toEntity();
            list($url, $body) = $accessTokenEntity->retrieveRequestItems();

            $response = $this->guzzleService->requestWithBody($url, $body);
            $request->storeAccessTokenToSession(json_decode($response->getBody()));

            $result = [
                'status' => 200,
                'message' => 'アクセストークンを取得しました。',
            ];
        } catch (\Exception $e) {
            $result = [
                'status' => $e->getCode(),
                'error' => $e->getMessage(),
            ];
        }

        return redirect()->route('main.index');
        // return response()->json($result);
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
        // $this->spotifyService->retrievePlaylistId($accessToken, $playlistName);
        $this->spotifyService->retrieveCurrentPlayLists($accessToken);
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
