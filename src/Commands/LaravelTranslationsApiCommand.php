<?php

namespace Samehdoush\LaravelTranslationsApi\Commands;

use Illuminate\Console\Command;

class LaravelTranslationsApiCommand extends Command
{
    public $signature = 'laravel-translations-api';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
