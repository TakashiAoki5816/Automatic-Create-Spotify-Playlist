<?php

namespace App\Http\Services;

use GuzzleHttp\Client;

class SpotifyService
{
    protected $client;
    /**
     * SpotifyService Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
