<?php

namespace Samehdoush\LaravelTranslationsApi\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Samehdoush\LaravelTranslationsApi\Models\Phrase;
use Samehdoush\LaravelTranslationsApi\Models\Translation;
use Samehdoush\LaravelTranslationsApi\Models\TranslationFile;

class PhraseFactory extends Factory
{
    protected $model = Phrase::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'phrase_id' => Phrase::factory(),
            'key' => $this->faker->unique()->word(),
            'translation_id' => Translation::factory(),
            'translation_file_id' => TranslationFile::factory(),
            'group' => $this->faker->word(),
            'value' => $this->faker->sentence(),
            'parameters' => [],
        ];
    }

    public function withParameters(): self
    {
        return $this->state([
            'parameters' => [
                'param1' => $this->faker->word(),
                'param2' => $this->faker->word(),
            ],
        ]);
    }
}
