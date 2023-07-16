<?php

use Illuminate\Support\Facades\Route;
use Samehdoush\LaravelTranslationsApi\Http\Controllers\TranslationController;


Route::middleware('api')->prefix('api')->group(function () {

    Route::controller(TranslationController::class)
        ->as('translations.')
        ->prefix('translations')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::delete('delete/{translation}', 'delete')->name('delete');
            Route::get('{translation}/progress', 'getTranslationProgressPercentage')->name('progress');
            // createSourceKey
            Route::post('createSourceKey', 'createSourceKey')->name('create');
            // createTranslation
            Route::post('createTranslation', 'createTranslation')->name('create');

            Route::prefix('phrases')
                ->as('phrases.')
                ->group(function () {
                    Route::get('{translation}', 'phrases')->name('index');
                    Route::get('{translation}/edit/{phrase:uuid}', 'phrase')->name('show');
                    Route::put('{translation}/edit/{phrase:uuid}', 'updatePhrase')->name('update');
                    Route::delete('{translation}/delete/{phrase:uuid}', 'deletePhrase')->name('delete');
                });
        });
});
