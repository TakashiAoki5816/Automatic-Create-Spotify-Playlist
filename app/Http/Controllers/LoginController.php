<?php

namespace App\Http\Controllers;

use App\Http\Services\SpotifyService;
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
    public function authorizeUrl(): JsonResponse
    {
        try {
            $result = [
                'status' => 200,
                'url' => $this->spotifyService->getAuthorizeUrl(),
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
