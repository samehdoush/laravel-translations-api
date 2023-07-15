<?php

namespace Samehdoush\LaravelTranslationsApi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Samehdoush\LaravelTranslationsApi\Models\Concerns\HasDatabaseConnection;
use Samehdoush\LaravelTranslationsApi\Models\Concerns\HasUuid;

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
}
