<?php

declare(strict_types=1);

use App\Modules\Religions\Http\Controllers\ReligionsCrudController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('admin/religions')->group(function (): void {
    Route::get('/{entity}', [ReligionsCrudController::class, 'index']);
    Route::post('/{entity}', [ReligionsCrudController::class, 'store']);
    Route::match(['put', 'patch'], '/{entity}/{id}', [ReligionsCrudController::class, 'update'])->whereNumber('id');
    Route::delete('/{entity}/{id}', [ReligionsCrudController::class, 'destroy'])->whereNumber('id');
});
