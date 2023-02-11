<?php

namespace App\Entities;

use App\Values\Code;
use JsonSerializable;
use App\Values\ClientId;
use App\Values\GrantType;
use App\Values\RedirectUrl;
use App\Values\ClientSecret;
use GuzzleHttp\Client;

/**
 * [アクセストークンエンティティ]
 */
class AccessTokenEntity implements JsonSerializable
{
    private readonly ClientId $clientId;
    private readonly ClientSecret $clientSecret;
    private readonly GrantType $grantType;
    private readonly Code $code;
    private readonly RedirectUrl $redirectUrl;

    protected function clientId(): ClientId
    {
        return $this->clientId;
    }

    protected function clientSecret(): ClientSecret
    {
        return $this->clientSecret;
    }

    protected function grantType(): GrantType
    {
        return $this->grantType;
    }

    protected function code(): Code
    {
        return $this->code;
    }

    protected function redirectUrl(): RedirectUrl
    {
        return $this->redirectUrl;
    }

    public function jsonSerialize(): array
    {
        return [];
    }

    /**
     * リクエストに必要なURL, Bodyを取得
     *
     * @return array
     */
    public function retrieveRequestItems(): array
    {
        $url = config('spotify.auth.access_token_url');
        $body = [
            'form_params' => [
                'client_id' => $this->clientId()->value(),
                'client_secret' => $this->clientSecret()->value(),
                'grant_type' => $this->grantType()->value(),
                'code' => $this->code()->value(),
                'redirect_uri' => $this->redirectUrl()->value(),
            ]
        ];

        return [$url, $body];
    }

    public function __construct(
        ClientId $clientId,
        ClientSecret $clientSecret,
        GrantType $grantType,
        Code $code,
        RedirectUrl $redirectUrl,
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->grantType = $grantType;
        $this->code = $code;
        $this->redirectUrl = $redirectUrl;
    }
}
