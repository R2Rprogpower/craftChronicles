<?php

declare(strict_types=1);

use App\Modules\GratitudeVariants\Http\Controllers\GratitudeVariantController;
use App\Modules\GratitudeVariants\Http\Middleware\EnsureGratitudeVariantEditorIp;
use Illuminate\Support\Facades\Route;

Route::get('/gratitude-variants', [GratitudeVariantController::class, 'index'])
    ->name('gratitude-variants.index');

Route::post('/gratitude-variants', [GratitudeVariantController::class, 'store'])
    ->middleware([EnsureGratitudeVariantEditorIp::class, 'throttle:30,1'])
    ->name('gratitude-variants.store');

Route::put('/gratitude-variants/{gratitudeVariant}', [GratitudeVariantController::class, 'update'])
    ->middleware([EnsureGratitudeVariantEditorIp::class, 'throttle:30,1'])
    ->name('gratitude-variants.update');

Route::delete('/gratitude-variants/{gratitudeVariant}', [GratitudeVariantController::class, 'destroy'])
    ->middleware([EnsureGratitudeVariantEditorIp::class, 'throttle:30,1'])
    ->name('gratitude-variants.destroy');
