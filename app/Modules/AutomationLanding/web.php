<?php

declare(strict_types=1);

use App\Modules\AutomationLanding\Http\Controllers\AutomationLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/ai-automation', [AutomationLandingController::class, 'index'])->name('automation-landing');
Route::post('/ai-automation/request', [AutomationLandingController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('automation-landing.request');
Route::get('/openclaw', [AutomationLandingController::class, 'short'])->name('openclaw-short');
Route::post('/openclaw/request', [AutomationLandingController::class, 'storeShort'])
    ->middleware('throttle:6,1')
    ->name('openclaw-short.request');
