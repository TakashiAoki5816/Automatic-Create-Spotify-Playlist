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
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->session()->get('access_token');
        if (empty($accessToken)) {
            return redirect()->route('authorizeUrl');
        }

        return $next($request);
    }
}
