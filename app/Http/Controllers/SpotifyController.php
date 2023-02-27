<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessTokenRequest;
use App\Http\Requests\AuthorizeRequest;
use App\Http\Services\GuzzleService;
use App\Http\Services\SpotifyService;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @return JsonResponse
     */
    public function authorization(AuthorizeRequest $request): JsonResponse
    {
        try {
            $authorizeEntity = $request->toEntity();
            $result = [
                'status' => 200,
                'url' => $authorizeEntity->retrieveRequestUrl(),
            ];
        } catch (\Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
        return response()->json($result, $result['status']);
    }

    /**
     * Access Tokenを取得し、セッションに格納
     *
     * @param AccessTokenRequest $request
     * @return JsonResponse
     */
    public function getAccessToken(AccessTokenRequest $request): JsonResponse
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
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($result);
    }

    /**
     * オリジナルのプレイリストを作成
     *
     * @param Request $request
     * @return
     */
    public function createPlayList(Request $request)
    {
        $accessToken = $request->session()->get('access_token');
        // $this->spotifyService->createPlayList($accessToken);
        // プレイリストから曲を取得
        $response = $this->spotifyService->fetchItemsFromPlaylist($accessToken);
        // $response2 = $this->spotifyService->fetchTrackDetails($accessToken); // これいらないかも
        // genresから何もJ-POP, K-POP, 洋楽とするか定める必要があるgenresテーブルを作成する　 etc. ONE OK ROCK j-pop, j-poprock, j-rock    Blueno Mars pop, dance pop    BTS k-pop, k-pop boy group
        // $response3 = $this->spotifyService->fetchArtistData($accessToken);

        $content = json_decode($response->getBody());


        // $response = $this->spotifyService->addItemToPlaylist($accessToken);
        return response()->json($response);
    }
}
