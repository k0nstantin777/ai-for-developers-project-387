<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\EventType;
use App\Repositories\EventTypeRepository;
use App\Services\EventTypeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTypeServiceTest extends TestCase
{
    use RefreshDatabase;

    private EventTypeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EventTypeService(new EventTypeRepository);
    }

    public function test_create_event_type(): void
    {
        $eventType = $this->service->create([
            'name' => 'Unit Test Event',
            'description' => 'Unit test description',
            'duration' => 45,
        ]);

        $this->assertNotNull($eventType->id);
        $this->assertEquals('Unit Test Event', $eventType->name);
        $this->assertEquals(45, $eventType->duration);
        $this->assertDatabaseHas('event_types', ['name' => 'Unit Test Event']);
    }

    public function test_list_returns_all_event_types(): void
    {
        EventType::factory()->count(3)->create();

        $result = $this->service->list();

        $this->assertCount(3, $result);
    }

    public function test_get_returns_single_event_type(): void
    {
        $eventType = EventType::factory()->create();

        $result = $this->service->get($eventType->id);

        $this->assertNotNull($result);
        $this->assertEquals($eventType->id, $result->id);
    }

    public function test_get_returns_null_for_missing(): void
    {
        $result = $this->service->get(999);

        $this->assertNull($result);
    }
}
