<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

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
     * リクエストボディ付きリクエスト
     *
     * @param string $url
     * @param array $body
     * @return ResponseInterface
     */
    public function requestWithBody(string $url, array $body): ResponseInterface
    {
        return $this->guzzleClient->request(
            'POST',
            $url,
            $body
        );
    }
}
