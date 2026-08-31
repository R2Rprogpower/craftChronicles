<?php

declare(strict_types=1);

use App\Modules\PersonalBrand\Http\Controllers\BrandPlatformController;
use Illuminate\Support\Facades\Route;

Route::get('/portfolio', [BrandPlatformController::class, 'portfolio'])->name('portfolio');
Route::get('/progress', [BrandPlatformController::class, 'progress'])->name('brand-progress');
