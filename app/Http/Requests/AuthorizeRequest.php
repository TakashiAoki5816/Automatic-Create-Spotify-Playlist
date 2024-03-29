<?php

namespace App\Http\Requests;

use App\Values\Scope;
use App\Values\ClientId;
use App\Values\AccountUrl;
use App\Values\RedirectUrl;
use App\Values\ResponseType;
use App\Entities\AuthorizeEntity;
use Illuminate\Foundation\Http\FormRequest;

class AuthorizeRequest extends FormRequest
{
    private static $scope = 'playlist-modify-public';

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
     * エンティティ化
     *
     * @return AuthorizeEntity
     */
    public function toEntity(): AuthorizeEntity
    {
        return new AuthorizeEntity(
            new AccountUrl(AccountUrl::BASE_URL),
            new ClientId(config('spotify.auth.client_id')),
            new ResponseType(),
            new RedirectUrl(config('spotify.auth.redirect_url')),
            new Scope(self::$scope),
        );
    }
}
