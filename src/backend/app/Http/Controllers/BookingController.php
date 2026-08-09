<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function index(): JsonResponse
    {
        $bookings = $this->bookingService->list();

        return response()->json(BookingResource::collection($bookings));
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $data = [
            'event_type_id' => $validated['eventTypeId'],
            'guest_name' => $validated['guestName'],
            'guest_email' => $validated['guestEmail'],
            'start_time' => $validated['startTime'],
        ];

        try {
            $booking = $this->bookingService->create($data);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(new BookingResource($booking), 201);
    }

    public function show(int $id): JsonResponse
    {
        $booking = $this->bookingService->get($id);

        if ($booking === null) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        return response()->json(new BookingResource($booking));
    }
}
