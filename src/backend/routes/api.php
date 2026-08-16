<?php

declare(strict_types=1);

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventTypeController;
use Illuminate\Support\Facades\Route;

Route::apiResource('event-types', EventTypeController::class)->only(['index', 'store', 'show']);
Route::get('event-types/{id}/slots', [EventTypeController::class, 'slots']);

Route::apiResource('bookings', BookingController::class)->only(['index', 'store', 'show']);

if (app()->environment('local')) {
    require __DIR__.'/testing.php';
}
