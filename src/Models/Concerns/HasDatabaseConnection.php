<?php

namespace Samehdoush\LaravelTranslationsApi\Models\Concerns;

trait HasDatabaseConnection
{
    public function getConnectionName()
    {
        if ($connection = config('translations.database_connection')) {
            return $connection;
        }

        return parent::getConnectionName();
    }
}
