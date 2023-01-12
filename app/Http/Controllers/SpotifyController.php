<?php

namespace App\Http\Controllers;

use App\Http\Services\SpotifyService;
use Spotify;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    protected $spotifyService;
    /**
     * SpotifyController Constructor
     *
     * @param Spotify $spotify
     */
    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
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

        $accessToken = $response->body('access_token');
        // $request->session()->put('access_token', 'セッションテスト');

        return response()->json($accessToken);
    }

    /**
     * プレイリスト作成テスト
     *
     * @return
     */
    public function createPlaylist()
    {
        $result = Spotify::playlist('34Q0PGji6l8u7MyH2RVTYl')->get();
        // https://open.spotify.com/playlist/34Q0PGji6l8u7MyH2RVTYl?si=68db44cb3f2f4106
        // またSpotify Libraryを使用すると、Access Tokenのことを気にせずとも使用できそう ← 単純に　Access Tokenを使用したAPIを使用していないだけの可能性あり
        // ただ技術者以外は使用するのが難しそう　 git導入・開発環境構築済みが前提になるため　← clone前提での使用とするか
        // https://open.spotify.com/playlist/4e5bQTFkklvb1l2Go242Q6?si=93d1867b52c84738
        // $result = Spotify::artist('726WiFmWkohzodUxK3XjHX?si=nTayRcXtTBSf66Fl1-s6_Q')->get();

        return response()->json($result);
    }
}
