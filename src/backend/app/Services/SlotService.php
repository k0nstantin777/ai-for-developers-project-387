<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\ValueObjects\TimeSlot;
use App\Models\EventType;
use App\Repositories\BookingRepository;
use DateTimeImmutable;

class SlotService
{
    private const WORKING_HOUR_START = 9;

    private const WORKING_HOUR_END = 18;

    private const DAYS_AHEAD = 14;

    public function __construct(
        private readonly BookingRepository $bookingRepository,
    ) {}

    /** @return array<int, array{startTime: string, endTime: string}> */
    public function getSlots(EventType $eventType): array
    {
        $today = new DateTimeImmutable('today');
        $endDate = $today->modify('+'.self::DAYS_AHEAD.' days');

        $range = new TimeSlot(
            $today->setTime(0, 0),
            $endDate->setTime(23, 59, 59),
        );

        $bookings = $this->bookingRepository->findByDateRange($range);
        $bookedSlots = array_map(function ($booking) {
            return new TimeSlot(
                new DateTimeImmutable($booking->start_time->toDateTimeString()),
                new DateTimeImmutable($booking->end_time->toDateTimeString()),
            );
        }, $bookings->all());

        $duration = $eventType->duration;
        $slots = [];

        for ($day = 0; $day < self::DAYS_AHEAD; $day++) {
            $date = $today->modify('+'.$day.' days');
            $slotHour = self::WORKING_HOUR_START;

            while ($slotHour * 60 + $duration <= self::WORKING_HOUR_END * 60) {
                $slotStart = $date->setTime(
                    (int) floor($slotHour),
                    ($slotHour * 60) % 60,
                );
                $slotEnd = $slotStart->modify('+'.$duration.' minutes');
                $slot = new TimeSlot($slotStart, $slotEnd);

                $isFree = true;
                foreach ($bookedSlots as $bookedSlot) {
                    if ($slot->overlaps($bookedSlot)) {
                        $isFree = false;
                        break;
                    }
                }

                if ($isFree) {
                    $slots[] = [
                        'startTime' => $slotStart->format('c'),
                        'endTime' => $slotEnd->format('c'),
                    ];
                }

                $slotHour += $duration / 60;
            }
        }

        return $slots;
    }
}
