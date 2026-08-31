<?php

declare(strict_types=1);

use App\Modules\ServiceRequests\Http\Controllers\ServiceRequestController;
use Illuminate\Support\Facades\Route;

Route::post('/service-requests', [ServiceRequestController::class, 'store'])->middleware('throttle:10,1')->name('service-requests.store');
