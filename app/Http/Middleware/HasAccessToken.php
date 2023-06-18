<?php

namespace App\Http\Middleware;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;

class HasAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        // アクセストークン 存在チェック
        if (empty($request->session()->get('access_token'))) {
            // 認証ルーティングにリダイレクト
            return redirect()->route('spotify.authorization');
        }

        $carbonExpiresIn = new CarbonImmutable($request->session()->get('expires_in'));
        // アクセストークン 期限切れチェック
        if ($carbonExpiresIn->isPast()) {
            // リフレッシュトークンをアクセストークンとしてセッションに格納する（現状リフレッシュトークンを使用しても上手くいってなさそう）
            $request->session()->put('access_token', $request->session()->get('refresh_token'));
            // TODO リフレッシュトークンも期限切れた場合
        }

        return $next($request);
    }
}
