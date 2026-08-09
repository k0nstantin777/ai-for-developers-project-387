<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\EventType;
use App\Repositories\BookingRepository;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlotServiceTest extends TestCase
{
    use RefreshDatabase;

    private SlotService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SlotService(new BookingRepository);
    }

    public function test_generates_slots_for_14_days(): void
    {
        $eventType = EventType::factory()->create(['duration' => 60]);

        $slots = $this->service->getSlots($eventType);

        $this->assertNotEmpty($slots);
        $this->assertArrayHasKey('startTime', $slots[0]);
        $this->assertArrayHasKey('endTime', $slots[0]);
    }

    public function test_slots_fall_within_14_day_window(): void
    {
        $eventType = EventType::factory()->create(['duration' => 60]);
        $today = now()->startOfDay();
        $maxDate = $today->copy()->addDays(14);

        $slots = $this->service->getSlots($eventType);

        foreach ($slots as $slot) {
            $startTime = Carbon::parse($slot['startTime']);
            $this->assertGreaterThanOrEqual($today, $startTime);
            $this->assertLessThan($maxDate, $startTime);
        }
    }

    public function test_slots_respect_working_hours(): void
    {
        $eventType = EventType::factory()->create(['duration' => 60]);

        $slots = $this->service->getSlots($eventType);

        foreach ($slots as $slot) {
            $startTime = Carbon::parse($slot['startTime']);
            $endTime = Carbon::parse($slot['endTime']);
            $this->assertGreaterThanOrEqual(9, (int) $startTime->format('G'));
            $this->assertLessThanOrEqual(18, (int) $endTime->format('G'));
        }
    }

    public function test_excludes_booked_slots(): void
    {
        $eventType = EventType::factory()->create(['duration' => 30]);
        $startTime = now()->addDays(2)->setTime(10, 0, 0);

        Booking::factory()->create([
            'event_type_id' => $eventType->id,
            'start_time' => $startTime,
            'end_time' => (clone $startTime)->addMinutes(30),
        ]);

        $slots = $this->service->getSlots($eventType);

        foreach ($slots as $slot) {
            $slotStart = Carbon::parse($slot['startTime']);
            $this->assertNotEquals(
                $startTime->format('Y-m-d H:i'),
                $slotStart->format('Y-m-d H:i'),
            );
        }
    }
}
