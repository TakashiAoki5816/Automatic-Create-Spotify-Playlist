<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        $accessToken = $request->session()->get('access_token');
        if (empty($accessToken)) {
            return redirect()->route('authorizeUrl');
        }

        return $next($request);
    }
}
