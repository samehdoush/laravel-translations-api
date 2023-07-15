<?php

namespace Samehdoush\LaravelTranslationsApi\Http\Middleware;

use Samehdoush\LaravelTranslationsApi\LaravelTranslationsApi;

class Authorize
{
    public function handle($request, $next)
    {
        return LaravelTranslationsApi::check($request) ? $next($request) : abort(403);
    }
}
