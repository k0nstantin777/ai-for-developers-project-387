<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TestingController extends Controller
{
    public function resetDatabase(): JsonResponse
    {
        DB::table('bookings')->delete();
        DB::table('event_types')->delete();

        return response()->json(['status' => 'ok']);
    }
}
