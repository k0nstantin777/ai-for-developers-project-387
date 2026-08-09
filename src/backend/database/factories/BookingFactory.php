<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $eventType = EventType::factory()->create();
        $startTime = now()->addDays(rand(0, 13))->setTime(rand(9, 15), 0, 0);
        $endTime = (clone $startTime)->addMinutes($eventType->duration);

        return [
            'event_type_id' => $eventType->id,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
