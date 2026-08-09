<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\EventType;
use App\Repositories\BookingRepository;
use App\Repositories\EventTypeRepository;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingService(
            new BookingRepository,
            new EventTypeRepository,
        );
    }

    public function test_create_booking_calculates_end_time(): void
    {
        $eventType = EventType::factory()->create(['duration' => 30]);
        $startTime = now()->addDay()->setTime(10, 0, 0);

        $booking = $this->service->create([
            'event_type_id' => $eventType->id,
            'guest_name' => 'John',
            'guest_email' => 'john@example.com',
            'start_time' => $startTime->format('Y-m-d H:i:s'),
        ]);

        $this->assertNotNull($booking->id);
        $this->assertEquals($startTime->format('Y-m-d H:i:s'), $booking->start_time->format('Y-m-d H:i:s'));
        $this->assertEquals(
            $startTime->copy()->addMinutes(30)->format('Y-m-d H:i:s'),
            $booking->end_time->format('Y-m-d H:i:s'),
        );
    }

    public function test_create_booking_rejects_overlap(): void
    {
        $eventType = EventType::factory()->create(['duration' => 60]);
        $startTime = now()->addDay()->setTime(10, 0, 0);

        $this->service->create([
            'event_type_id' => $eventType->id,
            'guest_name' => 'First',
            'guest_email' => 'first@example.com',
            'start_time' => $startTime->format('Y-m-d H:i:s'),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The selected time slot is already booked.');

        $this->service->create([
            'event_type_id' => $eventType->id,
            'guest_name' => 'Second',
            'guest_email' => 'second@example.com',
            'start_time' => $startTime->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_create_booking_allows_adjacent_slots(): void
    {
        $eventType = EventType::factory()->create(['duration' => 30]);
        $firstStart = now()->addDay()->setTime(10, 0, 0);

        $this->service->create([
            'event_type_id' => $eventType->id,
            'guest_name' => 'First',
            'guest_email' => 'first@example.com',
            'start_time' => $firstStart->format('Y-m-d H:i:s'),
        ]);

        $secondStart = $firstStart->copy()->addMinutes(30);

        $booking = $this->service->create([
            'event_type_id' => $eventType->id,
            'guest_name' => 'Second',
            'guest_email' => 'second@example.com',
            'start_time' => $secondStart->format('Y-m-d H:i:s'),
        ]);

        $this->assertNotNull($booking->id);
    }

    public function test_list_returns_all_bookings(): void
    {
        $eventType = EventType::factory()->create();
        $this->service->create([
            'event_type_id' => $eventType->id,
            'guest_name' => 'John',
            'guest_email' => 'john@example.com',
            'start_time' => now()->addDays(1)->setTime(10, 0)->format('Y-m-d H:i:s'),
        ]);

        $result = $this->service->list();

        $this->assertCount(1, $result);
    }

    public function test_get_returns_single_booking(): void
    {
        $eventType = EventType::factory()->create();
        $booking = $this->service->create([
            'event_type_id' => $eventType->id,
            'guest_name' => 'John',
            'guest_email' => 'john@example.com',
            'start_time' => now()->addDays(1)->setTime(10, 0)->format('Y-m-d H:i:s'),
        ]);

        $result = $this->service->get($booking->id);

        $this->assertNotNull($result);
        $this->assertEquals($booking->id, $result->id);
    }
}
