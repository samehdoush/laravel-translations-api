<?php

namespace Samehdoush\LaravelTranslationsApi\Http\Middleware;

use Samehdoush\LaravelTranslationsApi\LaravelTranslations;

class Authorize
{
    public function handle($request, $next)
    {
        return LaravelTranslations::check($request) ? $next($request) : abort(403);
    }
}
