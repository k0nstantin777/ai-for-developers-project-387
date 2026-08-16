<?php

declare(strict_types=1);

use App\Http\Controllers\TestingController;
use Illuminate\Support\Facades\Route;

Route::post('testing/reset-database', [TestingController::class, 'resetDatabase']);
