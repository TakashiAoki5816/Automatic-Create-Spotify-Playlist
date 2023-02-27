<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class GuzzleService
{
    protected $guzzleClient;
    /**
     * GuzzleService Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * APIリクエスト
     *
     * @param string $accessToken
     * @param string $method
     * @param string $uri
     * @param array $formData
     * @return Response
     */
    public function requestToSpotify(string $accessToken, string $method, string $uri, array $formData = null): Response
    {
        return $this->guzzleClient->request(
            $method,
            config('spotify.auth.base_url') . $uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $formData,
            ],
        );
    }

    /**
     * リクエストボディ付きリクエスト
     *
     * @param string $url
     * @param array $body
     * @return ResponseInterface
     */
    public function requestWithBody(string $url, array $body): Response
    {
        return $this->guzzleClient->request(
            'POST',
            $url,
            $body
        );
    }
}
