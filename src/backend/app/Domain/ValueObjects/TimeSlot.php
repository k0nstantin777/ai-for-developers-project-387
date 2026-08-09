<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use DateTimeInterface;

readonly class TimeSlot
{
    public function __construct(
        public DateTimeInterface $startTime,
        public DateTimeInterface $endTime,
    ) {}

    public function overlaps(TimeSlot $other): bool
    {
        return $this->startTime < $other->endTime
            && $this->endTime > $other->startTime;
    }
}
