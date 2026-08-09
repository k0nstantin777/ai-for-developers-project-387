<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EventType> */
class EventTypeFactory extends Factory
{
    protected $model = EventType::class;

    private static array $presets = [
        ['name' => '30 Minute Meeting', 'description' => 'A quick 30-minute meeting to discuss your needs.', 'duration' => 30],
        ['name' => '1 Hour Consultation', 'description' => 'A comprehensive one-hour consultation session.', 'duration' => 60],
        ['name' => 'Quick Chat', 'description' => 'A brief 15-minute chat to answer your questions.', 'duration' => 15],
    ];

    private static int $presetIndex = 0;

    public function definition(): array
    {
        $preset = self::$presets[self::$presetIndex % count(self::$presets)];
        self::$presetIndex++;

        return [
            'name' => $preset['name'],
            'description' => $preset['description'],
            'duration' => $preset['duration'],
        ];
    }
}
