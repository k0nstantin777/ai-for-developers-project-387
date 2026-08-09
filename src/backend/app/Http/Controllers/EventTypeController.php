<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventTypeRequest;
use App\Http\Resources\EventTypeResource;
use App\Services\EventTypeService;
use App\Services\SlotService;
use Illuminate\Http\JsonResponse;

class EventTypeController extends Controller
{
    public function __construct(
        private readonly EventTypeService $eventTypeService,
        private readonly SlotService $slotService,
    ) {}

    public function index(): JsonResponse
    {
        $eventTypes = $this->eventTypeService->list();

        return response()->json(EventTypeResource::collection($eventTypes));
    }

    public function store(StoreEventTypeRequest $request): JsonResponse
    {
        $eventType = $this->eventTypeService->create($request->validated());

        return response()->json(new EventTypeResource($eventType), 201);
    }

    public function show(int $id): JsonResponse
    {
        $eventType = $this->eventTypeService->get($id);

        if ($eventType === null) {
            return response()->json(['message' => 'Event type not found.'], 404);
        }

        return response()->json(new EventTypeResource($eventType));
    }

    public function slots(int $id): JsonResponse
    {
        $eventType = $this->eventTypeService->get($id);

        if ($eventType === null) {
            return response()->json(['message' => 'Event type not found.'], 404);
        }

        $slots = $this->slotService->getSlots($eventType);

        return response()->json(['slots' => $slots]);
    }
}
