<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        EventType::factory()->createMany([
            ['name' => '30 Minute Meeting', 'description' => 'A quick 30-minute meeting to discuss your needs.', 'duration' => 30],
            ['name' => '1 Hour Consultation', 'description' => 'A comprehensive one-hour consultation session.', 'duration' => 60],
            ['name' => 'Quick Chat', 'description' => 'A brief 15-minute chat to answer your questions.', 'duration' => 15],
        ]);
    }
}
