<?php

namespace Samehdoush\LaravelTranslationsApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Samehdoush\LaravelTranslationsApi\Commands\LaravelTranslationsApiCommand;
use Samehdoush\LaravelTranslationsApi\Console\Commands\ExportTranslationsCommand;
use Samehdoush\LaravelTranslationsApi\Console\Commands\ImportTranslationsCommand;
use Samehdoush\LaravelTranslationsApi\Console\Commands\PublishCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

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
            ->name('translations-api')
            ->hasConfigFile()
            ->hasMigration('2023_07_15_100000_create_translations_tables')
            ->hasRoute('api')
            ->hasConsoleCommands([
                ExportTranslationsCommand::class,
                ImportTranslationsCommand::class,
                PublishCommand::class,
            ])->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Hello, and welcome to my great new package!');
                    })
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('samehdoush/laravel-translations-api')
                    ->endWith(function (InstallCommand $command) {
                        $command->info('Have a great day!');
                    });
            });
    }
}
