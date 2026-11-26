<?php

declare(strict_types=1);

use App\Modules\FamilyChronicles\Http\Controllers\FamilyChroniclesController;
use Illuminate\Support\Facades\Route;

Route::prefix('family-chronicles')
    ->middleware('auth')
    ->name('family-chronicles.')
    ->group(function (): void {
        Route::get('/', [FamilyChroniclesController::class, 'index'])->name('index');
        Route::get('/state', [FamilyChroniclesController::class, 'state'])->name('state');
        Route::post('/people', [FamilyChroniclesController::class, 'storePerson'])->name('people.store');
        Route::patch('/people/{person}', [FamilyChroniclesController::class, 'updatePerson'])->name('people.update');
        Route::post('/people/{person}/relatives', [FamilyChroniclesController::class, 'storeRelative'])->name('relatives.store');
        Route::delete('/relatives/{relative}', [FamilyChroniclesController::class, 'destroyRelative'])->name('relatives.destroy');
        Route::post('/answers', [FamilyChroniclesController::class, 'storeAnswer'])->name('answers.store');
        Route::post('/answers/{answer}/attachments', [FamilyChroniclesController::class, 'storeAttachment'])->name('attachments.store');
        Route::delete('/attachments/{attachment}', [FamilyChroniclesController::class, 'destroyAttachment'])->name('attachments.destroy');
        Route::get('/attachments/{attachment}', [FamilyChroniclesController::class, 'downloadAttachment'])->name('attachments.download');
    });
