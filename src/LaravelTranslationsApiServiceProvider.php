<?php

namespace Samehdoush\LaravelTranslationsApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Samehdoush\LaravelTranslationsApi\Commands\LaravelTranslationsApiCommand;

class LaravelTranslationsApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-translations-api')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-translations-api_table')
            ->hasCommand(LaravelTranslationsApiCommand::class);
    }
}
