<?php

namespace App\Http\Controllers;

use App\Http\Services\SpotifyService;
use App\Http\Requests\AuthorizeRequest;
use GuzzleHttp\Client;
use Spotify;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    protected $spotifyService;
    protected $guzzleClient;
    /**
     * SpotifyController Constructor
     *
     * @param Spotify $spotify
     */
    public function __construct(
        SpotifyService $spotifyService,
        Client $client,
    ) {
        $this->spotifyService = $spotifyService;
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
                'url' => $authorizeEntity->url(),
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
     * Access Tokenを取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccessToken(Request $request)
    {
        $response = $this->spotifyService->getAccessTokenRequest($request->input('code'));
        $accessToken = json_decode($response->body());
        $request->session()->put('access_token', $accessToken->access_token);

        return response()->json($response->body());
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
