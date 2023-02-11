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

    public function getUserProfile()
    {
        $accessToken = (new Request)->session()->get('access_token');
        $response = $this->guzzleClient->request(
            'GET',
            "https://api.spotify.com/v1/me",
            [
                'auth' => [
                    $accessToken
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
            ]
        );
        return $response;
    }

    /**
     * プレイリスト作成テスト
     *
     * @return
     */
    public function createPlaylist(Request $request)
    {
        $accessToken = $request->session()->get('access_token');
        $response = $this->guzzleClient->request(
            'GET',
            "https://api.spotify.com/v1/me",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
            ]
        );
        $bodyContent = json_decode($response->getBody()->getContents());
        $userId = $bodyContent->id;
        $response = $this->guzzleClient->request(
            'POST',
            "https://api.spotify.com/v1/users/{$userId}/playlists",
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    "name" => "Laravelテストプレイリスト作成",
                    "description" => "Laravelテストプレイリスト description",
                    "public" => true
                ]
            ]
        );

        return response()->json($response);
    }
}
