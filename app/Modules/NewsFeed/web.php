<?php

declare(strict_types=1);

use App\Modules\NewsFeed\Http\Controllers\NewsFeedController;
use App\Modules\NewsFeed\Http\Middleware\EnsureNewsFeedEditorIp;
use Illuminate\Support\Facades\Route;

Route::get('/news-feed', [NewsFeedController::class, 'index'])->name('news-feed.index');

Route::post('/news-feed/sources', [NewsFeedController::class, 'store'])
    ->middleware([EnsureNewsFeedEditorIp::class, 'throttle:30,1'])
    ->name('news-feed.sources.store');

Route::post('/news-feed/refresh', [NewsFeedController::class, 'refresh'])
    ->middleware([EnsureNewsFeedEditorIp::class, 'throttle:6,1'])
    ->name('news-feed.refresh');
