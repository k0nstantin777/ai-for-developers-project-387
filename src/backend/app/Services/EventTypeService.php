<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EventType;
use App\Repositories\EventTypeRepository;
use Illuminate\Database\Eloquent\Collection;

class EventTypeService
{
    public function __construct(
        private readonly EventTypeRepository $repository,
    ) {}

    /** @return Collection<int, EventType> */
    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function get(int $id): ?EventType
    {
        return $this->repository->find($id);
    }

    public function create(array $data): EventType
    {
        return $this->repository->create($data);
    }
}
