<?php

namespace Samehdoush\LaravelTranslationsApi\TranslationLoaders;

use Samehdoush\LaravelTranslationsApi\Models\Phrase;


class Db implements TranslationLoader
{
    public function loadTranslations(string $locale, string $group): array
    {

        $model = config('translations-api.model');
        return $model::getTranslationsForGroup($locale, $group);
    }


}
