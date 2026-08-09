<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Domain\ValueObjects\TimeSlot;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository
{
    /** @return Collection<int, Booking> */
    public function all(): Collection
    {
        return Booking::with('eventType')->orderBy('start_time')->get();
    }

    public function find(int $id): ?Booking
    {
        return Booking::with('eventType')->find($id);
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function hasOverlappingSlots(TimeSlot $slot, ?int $excludeBookingId = null): bool
    {
        $query = Booking::query()
            ->where('start_time', '<', $slot->endTime->format('Y-m-d H:i:s'))
            ->where('end_time', '>', $slot->startTime->format('Y-m-d H:i:s'));

        if ($excludeBookingId !== null) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    /** @return Collection<int, Booking> */
    public function findByDateRange(TimeSlot $range): Collection
    {
        return Booking::query()
            ->where('start_time', '<', $range->endTime->format('Y-m-d H:i:s'))
            ->where('end_time', '>', $range->startTime->format('Y-m-d H:i:s'))
            ->orderBy('start_time')
            ->get();
    }
}
