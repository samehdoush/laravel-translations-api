<?php

namespace Samehdoush\LaravelTranslationsApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Samehdoush\LaravelTranslationsApi\Http\Middleware\Authorize;
use Samehdoush\LaravelTranslationsApi\Models\Language;
use Samehdoush\LaravelTranslationsApi\Models\Phrase;
use Samehdoush\LaravelTranslationsApi\Models\Translation;
use Samehdoush\LaravelTranslationsApi\Models\TranslationFile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Lang;

class TranslationController extends BaseController
{
    public function __construct()
    {
        $this->middleware(Authorize::class);
    }
    public function getTranslations(): LengthAwarePaginator
    {
        $search = request()->search;
        return Translation::orderByDesc('source')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('language', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                });
            })
            // with get Translation Progress Percentage
            ->withCount(['phrases as translated' => function ($query) {
                $query->whereNotNull('value');
            }])
            ->withCount(['phrases as untranslated' => function ($query) {
                $query->whereNull('value');
            }])
            ->withCount('phrases')

            ->with('language')->paginate(12)->onEachSide(0);
    }
    public function getTranslationProgressPercentage(Translation $translation): JsonResponse
    {
        $phrases = $translation
            ->phrases()->toBase()
            ->selectRaw('COUNT(CASE WHEN value IS NOT NULL THEN 1 END) AS translated')
            ->selectRaw('COUNT(CASE WHEN value IS NULL THEN 1 END) AS untranslated')
            ->selectRaw('COUNT(*) AS total')
            ->first();
        $percentage = round(($phrases->translated / $phrases->total) * 100, 2);
        return response()->json(['percentage' => $percentage]);
    }
    public function delete(Translation $translation): JsonResponse
    {
        DB::transaction(function () use ($translation) {
            $translation->phrases()->delete();
            $translation->delete();
        });
        return response()->json([
            'message' => 'Translation deleted successfully!',
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'installed' => Language::count() > 0 && Translation::count() > 0,
            'translations' => $this->getTranslations(),
        ]);
    }

    public function getPhrases(Translation $translation): LengthAwarePaginator
    {
        $search = request()->search;
        $status = request()->status;
        $perPage = request()->perPage ?? 20;
        return $translation->phrases()
            ->orderBy('key')
            ->with(['file', 'translation'])
            ->when($search, function ($query) use ($search) {
                $query->where('key', 'like', "%$search%")
                    ->orWhere('value', 'like', "%$search%");
            })
            ->when($status, function (Builder $query) use ($status) {
                if ($status == 1) {
                    $query->whereNotNull('value');
                } elseif ($status == 2) {
                    $query->whereNull('value');
                }
            })
            ->paginate($perPage)->onEachSide(0);
    }
    public function phrases(Translation $translation): JsonResponse
    {
        return response()->json([
            'phrases' => $this->getPhrases($translation),
        ]);
    }
    // delete phrase
    public function deletePhrase(Phrase $phrase): JsonResponse
    {
        if (!$phrase->translation->source) {
            return response()->json([
                'message' => 'You can only delete phrases from source translations!',
            ], 422);
        }
        $phrase->delete();
        return response()->json([
            'message' => 'Source key deleted successfully!',
        ]);
    }
    public function phrase(Translation $translation, Phrase $phrase): JsonResponse
    {
        return response()->json([
            'phrase' => $phrase,
            'translation' => $translation,
        ]);
    }
    public function updatePhrase(Phrase $phrase): JsonResponse
    {
        // validate  value is required
        request()->validate([
            'value' => 'required',
        ]);
        $phrase->update([
            'value' => request()->value,
        ]);
        $nextPhrase = $phrase->translation->phrases()->where('id', '>', $phrase->id)->whereNull('value')->first();
        if ($nextPhrase) {
            return response()->json([
                'message' => 'Phrase updated successfully!',
                'nextPhrase' => $nextPhrase,
                'nextPhraseUrl' => route('translations.phrases.show', [
                    'translation' => $phrase->translation,
                    'phrase' => $nextPhrase,
                ]),
            ]);
        }
        return response()->json([
            'message' => 'Phrase updated successfully!',
        ]);
    }

    // CreateSourceKey
    public function createSourceKey(): JsonResponse
    {

        request()->validate([
            'key' => 'required',
            'file' => 'required',
            'key_translation' => 'required',
        ]);
        $sourceTranslation = Translation::where('source', true)->first();
        $sourceKey = $sourceTranslation->phrases()->create([
            'key' => request()->key,
            'phrase_id' => null,
            'parameters' => null,
            'value' => request()->key_translation,
            'translation_file_id' => request()->file,
            'group' => TranslationFile::find(request()->file)->name,
        ]);
        foreach (Translation::where('source', false)->get() as $translation) {
            $translation->phrases()->create([
                'value' => null,
                'key' => $sourceKey->key,
                'group' => $sourceKey->group,
                'phrase_id' => $sourceKey->id,
                'parameters' => $sourceKey->parameters,
                'translation_file_id' => $sourceKey->file->id,
            ]);
        }

        return response()->json([
            'message' => 'Source key created successfully!',
        ]);
    }

    // CreateTranslation
    public function createTranslation(): JsonResponse
    {

        request()->validate([
            'language' => 'required|exists:ltu_languages,id|unique:ltu_translations,language_id',
        ]);
        $translation = Translation::create([
            'source' => false,
            'language_id' => request()->language,
        ]);

        $sourceTranslation = Translation::where('source', true)->first();

        foreach ($sourceTranslation->phrases()->with('file')->get() as $sourcePhrase) {
            $translation->phrases()->create([
                'value' => null,
                'key' => $sourcePhrase->key,
                'group' => $sourcePhrase->group,
                'phrase_id' => $sourcePhrase->id,
                'parameters' => $sourcePhrase->parameters,
                'translation_file_id' => $sourcePhrase->file->id,
            ]);
        }

        return response()->json([
            'message' => 'Translation created successfully!',
        ]);
    }
}
