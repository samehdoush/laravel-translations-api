<?php

namespace Samehdoush\LaravelTranslationsApi\TranslationLoaders;

use Samehdoush\LaravelTranslationsApi\Models\Phrase;


class Db implements TranslationLoader
{
    public function loadTranslations(string $locale, string $group): array
    {


        return Phrase::getTranslationsForGroup($locale, $group);
    }


}
