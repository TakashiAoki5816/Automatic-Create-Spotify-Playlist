<?php

namespace App\Entities;

use App\Values\AccountUrl;
use App\Values\ClientId;
use App\Values\RedirectUrl;
use App\Values\ResponseType;
use App\Values\Scope;
use JsonSerializable;

/**
 * [認証エンティティ]
 */
class AuthorizeEntity implements JsonSerializable
{
    private readonly AccountUrl $accountUrl;
    private readonly ClientId $clientId;
    private readonly ResponseType $responseType;
    private readonly RedirectUrl $redirectUrl;
    private readonly Scope $scope;

    protected function accountUrl(): AccountUrl
    {
        return $this->accountUrl;
    }

    protected function clientId(): ClientId
    {
        return $this->clientId;
    }

    protected function responseType(): ResponseType
    {
        return $this->responseType;
    }

    protected function redirectUrl(): RedirectUrl
    {
        return $this->redirectUrl;
    }

    protected function scope(): Scope
    {
        return $this->scope;
    }

    public function jsonSerialize(): array
    {
        return [];
    }

    /**
     * リクエストURLを返却
     *
     * @return string
     */
    public function retrieveRequestUrl(): string
    {
        return $this->accountUrl()->value() . '?client_id=' . $this->clientId()->value() . '&response_type=' . $this->responseType()->value() . '&redirect_uri=' . $this->redirectUrl()->value() . '&scope=' . $this->scope()->value() . '&show_dialog=true';
    }

    public function __construct(
        AccountUrl $accountUrl,
        ClientId $clientId,
        ResponseType $responseType,
        RedirectUrl $redirectUrl,
        Scope $scope,
    ) {
        $this->accountUrl = $accountUrl;
        $this->clientId = $clientId;
        $this->responseType = $responseType;
        $this->redirectUrl = $redirectUrl;
        $this->scope = $scope;
    }
}
