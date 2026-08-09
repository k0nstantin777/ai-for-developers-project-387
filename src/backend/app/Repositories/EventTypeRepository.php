<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventType;
use Illuminate\Database\Eloquent\Collection;

class EventTypeRepository
{
    /** @return Collection<int, EventType> */
    public function all(): Collection
    {
        return EventType::all();
    }

    public function find(int $id): ?EventType
    {
        return EventType::find($id);
    }

    public function create(array $data): EventType
    {
        return EventType::create($data);
    }
}
