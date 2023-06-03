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
        // セッション格納チェック
        if (empty($request->session()->get('access_token'))) {
            // 認証ルーティングにリダイレクト
            return redirect()->route('spotify.authorization');
        }

        $carbonExpiresIn = new CarbonImmutable($request->session()->get('expires_in'));
        // 期限切れチェック
        if ($carbonExpiresIn->isPast()) {
            // リフレッシュトークンをアクセストークンとしてセッションに格納する
            // TODO リフレッシュトークンも期限切れた場合
            $this->session()->put('access_token', $request->session()->get('refresh_token'));
        }

        return $next($request);
    }
}
