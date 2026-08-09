<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EventType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_event_types(): void
    {
        $eventTypes = EventType::factory()->count(3)->create();

        $response = $this->getJson('/api/event-types');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonFragment(['id' => $eventTypes->first()->id]);
    }

    public function test_store_creates_event_type(): void
    {
        $data = [
            'name' => 'Test Event',
            'description' => 'Test description',
            'duration' => 45,
        ];

        $response = $this->postJson('/api/event-types', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Test Event']);

        $this->assertDatabaseHas('event_types', [
            'name' => 'Test Event',
            'duration' => 45,
        ]);
    }

    public function test_show_returns_single_event_type(): void
    {
        $eventType = EventType::factory()->create();

        $response = $this->getJson('/api/event-types/'.$eventType->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $eventType->id]);
    }

    public function test_show_returns_404_for_missing_event_type(): void
    {
        $response = $this->getJson('/api/event-types/999');

        $response->assertStatus(404);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/event-types', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'description', 'duration']);
    }

    public function test_store_validates_duration_minimum(): void
    {
        $response = $this->postJson('/api/event-types', [
            'name' => 'Test',
            'description' => 'Test',
            'duration' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['duration']);
    }

    public function test_slots_returns_available_slots_for_14_days(): void
    {
        $eventType = EventType::factory()->create(['duration' => 30]);

        $response = $this->getJson('/api/event-types/'.$eventType->id.'/slots');

        $response->assertStatus(200);
        $response->assertJsonStructure(['slots']);
        $this->assertNotEmpty($response->json('slots'));
    }

    public function test_slots_returns_404_for_missing_event_type(): void
    {
        $response = $this->getJson('/api/event-types/999/slots');

        $response->assertStatus(404);
    }
}
