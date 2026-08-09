<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Booking */
class BookingResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'eventTypeId' => $this->event_type_id,
            'guestName' => $this->guest_name,
            'guestEmail' => $this->guest_email,
            'startTime' => $this->start_time?->toISOString(),
            'endTime' => $this->end_time?->toISOString(),
            'eventType' => new EventTypeResource($this->whenLoaded('eventType')),
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
