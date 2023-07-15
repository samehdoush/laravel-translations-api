<?php

namespace Samehdoush\LaravelTranslationsApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Samehdoush\LaravelTranslationsApi\LaravelTranslationsApi
 */
class LaravelTranslationsApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Samehdoush\LaravelTranslationsApi\LaravelTranslationsApi::class;
    }
}
