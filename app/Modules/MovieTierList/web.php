<?php

declare(strict_types=1);

use App\Modules\MovieTierList\Http\Controllers\MovieTierListController;
use App\Modules\MovieTierList\Http\Middleware\EnsureMovieTierListEditorIp;
use Illuminate\Support\Facades\Route;

Route::get('/movies-tier-list', [MovieTierListController::class, 'show'])
    ->name('movies-tier-list.show');

Route::put('/movies-tier-list', [MovieTierListController::class, 'update'])
    ->middleware([EnsureMovieTierListEditorIp::class, 'throttle:30,1'])
    ->name('movies-tier-list.update');
