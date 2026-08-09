<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\EventType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_bookings(): void
    {
        $eventType = EventType::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['event_type_id' => $eventType->id]);

        $response = $this->getJson('/api/bookings');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['id' => $bookings->first()->id]);
    }

    public function test_store_creates_booking(): void
    {
        $eventType = EventType::factory()->create(['duration' => 30]);
        $startTime = now()->addDays(1)->setTime(10, 0, 0);

        $response = $this->postJson('/api/bookings', [
            'eventTypeId' => $eventType->id,
            'guestName' => 'John Doe',
            'guestEmail' => 'john@example.com',
            'startTime' => $startTime->toISOString(),
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['guestName' => 'John Doe']);

        $this->assertDatabaseHas('bookings', [
            'event_type_id' => $eventType->id,
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
        ]);
    }

    public function test_show_returns_single_booking(): void
    {
        $eventType = EventType::factory()->create();
        $booking = Booking::factory()->create(['event_type_id' => $eventType->id]);

        $response = $this->getJson('/api/bookings/'.$booking->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $booking->id]);
    }

    public function test_show_returns_404_for_missing_booking(): void
    {
        $response = $this->getJson('/api/bookings/999');

        $response->assertStatus(404);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/bookings', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['eventTypeId', 'guestName', 'guestEmail', 'startTime']);
    }

    public function test_store_validates_event_type_exists(): void
    {
        $response = $this->postJson('/api/bookings', [
            'eventTypeId' => 999,
            'guestName' => 'John',
            'guestEmail' => 'john@example.com',
            'startTime' => now()->toISOString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['eventTypeId']);
    }

    public function test_store_rejects_overlapping_booking_same_time(): void
    {
        $eventType = EventType::factory()->create(['duration' => 30]);
        $startTime = now()->addDays(1)->setTime(10, 0, 0);

        Booking::factory()->create([
            'event_type_id' => $eventType->id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes(30),
        ]);

        $response = $this->postJson('/api/bookings', [
            'eventTypeId' => $eventType->id,
            'guestName' => 'Jane Doe',
            'guestEmail' => 'jane@example.com',
            'startTime' => $startTime->toISOString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'The selected time slot is already booked.']);
    }

    public function test_store_rejects_overlapping_booking_partial_overlap(): void
    {
        $eventType = EventType::factory()->create(['duration' => 60]);
        $startTime = now()->addDays(1)->setTime(10, 0, 0);

        Booking::factory()->create([
            'event_type_id' => $eventType->id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes(60),
        ]);

        $response = $this->postJson('/api/bookings', [
            'eventTypeId' => $eventType->id,
            'guestName' => 'Jane Doe',
            'guestEmail' => 'jane@example.com',
            'startTime' => $startTime->copy()->addMinutes(30)->toISOString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'The selected time slot is already booked.']);
    }
}
