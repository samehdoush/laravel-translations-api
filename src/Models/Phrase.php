<?php

namespace Samehdoush\LaravelTranslationsApi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Samehdoush\LaravelTranslationsApi\Models\Concerns\HasDatabaseConnection;
use Samehdoush\LaravelTranslationsApi\Models\Concerns\HasUuid;
use Illuminate\Support\Arr;

class Phrase extends Model
{
    use HasUuid;
    use HasFactory;
    use HasDatabaseConnection;

    protected $guarded = [];

    protected $table = 'ltu_phrases';

    protected $casts = [
        'parameters' => 'array',
    ];

    protected $with = [
        'source',
    ];
    public static function boot()
    {
        parent::boot();

        $flushGroupCache = function (self $languageLine) {
            $languageLine->flushGroupCache();
        };

        static::saved($flushGroupCache);
        static::deleted($flushGroupCache);
    }
    public function file(): BelongsTo
    {
        return $this->belongsTo(TranslationFile::class, 'translation_file_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Phrase::class, 'phrase_id');
    }

    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::class);
    }

    public static function getTranslationsForGroup(string $locale, string $group): array
    {
        return Cache::rememberForever(static::getCacheKey($group, $locale), function () use ($group, $locale) {
            return static::query()
                ->where('group', $group)
                ->get()
                ->reduce(function ($lines, self $languageLine) use ($group, $locale) {
                    // $translation = $languageLine->getTranslation($locale);
                    $translation = $languageLine->value;

                    if ($translation !== null && $group === '*') {
                        // Make a flat array when returning json translations
                        $lines[$languageLine->key] = $translation;
                    } elseif ($translation !== null && $group !== '*') {
                        // Make a nested array when returning normal translations
                        Arr::set($lines, $languageLine->key, $translation);
                    }

                    return $lines;
                }) ?? [];
        });
    }

    // public function getTranslation(string $locale): ?string
    // {
    //     if (!isset($this->text[$locale])) {
    //         $fallback = config('app.fallback_locale');

    //         return $this->text[$fallback] ?? null;
    //     }

    //     return $this->text[$locale];
    // }
    public static function getCacheKey(string $group, string $locale): string
    {
        return "samehdoush.translation-loader.{$group}.{$locale}";
    }
    public function flushGroupCache()
    {
        foreach ($this->getTranslatedLocales() as $locale) {
            Cache::forget(static::getCacheKey($this->group, $locale));
        }
    }

    protected function getTranslatedLocales(): array
    {
        return Translation::with('language')
            ->get()
            ->pluck('language.code')
            ->toArray();
    }
}
