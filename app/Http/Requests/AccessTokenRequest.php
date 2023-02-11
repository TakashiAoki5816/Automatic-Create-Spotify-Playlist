<?php

namespace App\Http\Requests;

use App\Entities\AccessTokenEntity;
use App\Values\ClientId;
use App\Values\ClientSecret;
use App\Values\Code;
use App\Values\GrantType;
use App\Values\RedirectUrl;
use Illuminate\Foundation\Http\FormRequest;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class AccessTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * アクセストークンをセッションに格納
     *
     * @param stdClass $stdClass
     * @return void
     */
    public function storeAccessTokenToSession(stdClass $stdClass): void
    {
        $this->session()->put('access_token', $stdClass->access_token);
    }

    /**
     * @return AccessTokenEntity
     */
    public function toEntity()
    {
        $code = $this->input('code');

        return new AccessTokenEntity(
            new ClientId(config('spotify.auth.client_id')),
            new ClientSecret(config('spotify.auth.client_secret')),
            new GrantType(),
            new Code($code),
            new RedirectUrl(config('spotify.auth.redirect_url')),
        );
    }
}
