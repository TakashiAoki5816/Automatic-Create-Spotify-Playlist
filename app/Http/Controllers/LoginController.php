<?php

namespace App\Http\Controllers;

use App\Http\Services\SpotifyService;
use App\Http\Requests\AuthorizeRequest;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    protected $spotifyService;
    /**
     * LoginController Constructor
     *
     * @param SpotifyService $spotifyService
     */
    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    /**
     * 認可したいURLを返却
     *
     * @return JsonResponse
     */
    public function authorizeUrl(AuthorizeRequest $request): JsonResponse
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
}
