<?php

namespace Samehdoush\LaravelTranslationsApi;

use Illuminate\Database\QueryException;
use Illuminate\Translation\FileLoader;
use Illuminate\Support\Facades\Schema;
use Samehdoush\LaravelTranslationsApi\Models\Phrase;
use Samehdoush\LaravelTranslationsApi\TranslationLoaders\Db;
use Samehdoush\LaravelTranslationsApi\TranslationLoaders\TranslationLoader;

class TranslationLoaderManager extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        try {
            $fileTranslations = parent::load($locale, $group, $namespace);

            if (!is_null($namespace) && $namespace !== '*') {
                return $fileTranslations;
            }

            $loaderTranslations = $this->getTranslationsForTranslationLoaders($locale, $group, $namespace);

            return array_replace_recursive($fileTranslations, $loaderTranslations);
        } catch (QueryException $e) {
            $modelClass = Phrase::class;
            $model = new $modelClass;
            if (is_a($model, Phrase::class)) {
                if (!Schema::hasTable($model->getTable())) {
                    return parent::load($locale, $group, $namespace);
                }
            }

            throw $e;
        };
    }

    protected function getTranslationsForTranslationLoaders(
        string $locale,
        string $group,
        string $namespace = null
    ): array {
        return collect(Db::class)
            ->map(function (string $className) {
                return app($className);
            })
            ->mapWithKeys(function (TranslationLoader $translationLoader) use ($locale, $group, $namespace) {
                return $translationLoader->loadTranslations($locale, $group, $namespace);
            })
            ->toArray();
    }
}
