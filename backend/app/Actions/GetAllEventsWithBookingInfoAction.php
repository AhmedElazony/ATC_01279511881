<?php

namespace App\Actions;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GetAllEventsWithBookingInfoAction
{
    protected Builder $query;
    protected LengthAwarePaginator $events;
    public function __construct(protected ?string $userId, protected array $filters)
    {
    }

    public function execute()
    {
        $this->setQuery()
            ->getBookingInfo()
            ->getEventsPaginated()
            ->getEventResource();

        return $this->events;
    }

    public function setQuery()
    {
        $this->query = Event::with(['category', 'tags']);
        return $this;
    }

    protected function getBookingInfo()
    {
        $this->query->withBookingInfo($this->userId);

        return $this;
    }

    protected function getEventsPaginated()
    {
        $this->events = $this->query->latest()
            ->filter($this->filters)
            ->paginate(9);

        return $this;
    }

    protected function getEventResource()
    {
        $this->eventResource = EventResource::paginate($this->events);
        return $this;
    }
}
