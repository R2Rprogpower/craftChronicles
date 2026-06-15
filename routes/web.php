<?php

use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('root');

Route::get('/login', function () {
    return view('auth-login');
})->name('login');

Route::post('/login', [WebAuthController::class, 'login'])->name('login.submit');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::prefix('theme-preview')->group(function () {
    $previewPages = [
        'ui-alerts' => 'ui-alerts',
        'form-elements' => 'form-elements',
        'ui-rangeslider' => 'ui-rangeslider',
    ];

    Route::get('/', function () use ($previewPages) {
        $links = collect($previewPages)
            ->keys()
            ->map(fn (string $page) => [
                'name' => $page,
                'url' => route('theme-preview.page', ['page' => $page]),
            ]);

        return response()->json([
            'message' => 'Theme preview endpoints',
            'endpoints' => $links,
        ]);
    })->name('theme-preview.index');

    Route::get('/{page}', function (string $page) use ($previewPages) {
        abort_unless(array_key_exists($page, $previewPages), 404);

        return view($previewPages[$page]);
    })->name('theme-preview.page');
});
