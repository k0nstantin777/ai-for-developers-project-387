<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\ValueObjects\TimeSlot;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\EventTypeRepository;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class BookingService
{
    public function __construct(
        private readonly BookingRepository $repository,
        private readonly EventTypeRepository $eventTypeRepository,
    ) {}

    /** @return Collection<int, Booking> */
    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function get(int $id): ?Booking
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Booking
    {
        $eventType = $this->eventTypeRepository->find($data['event_type_id']);

        if ($eventType === null) {
            throw new RuntimeException('Event type not found.');
        }

        $startTime = new DateTimeImmutable($data['start_time']);
        $endTime = $startTime->modify('+'.$eventType->duration.' minutes');

        $slot = new TimeSlot($startTime, $endTime);

        if ($this->repository->hasOverlappingSlots($slot)) {
            throw new RuntimeException('The selected time slot is already booked.');
        }

        return $this->repository->create([
            'event_type_id' => $eventType->id,
            'guest_name' => $data['guest_name'],
            'guest_email' => $data['guest_email'],
            'start_time' => $startTime->format('Y-m-d H:i:s'),
            'end_time' => $endTime->format('Y-m-d H:i:s'),
        ]);
    }
}
