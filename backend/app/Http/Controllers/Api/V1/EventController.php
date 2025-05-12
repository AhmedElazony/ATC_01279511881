<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Resources\EventResource;
use App\Models\Event;

class EventController extends ApiController
{
    public function index()
    {
        return $this->success(
            EventResource::paginate(
                Event::with(['category', 'tags'])
                    ->filter(request()->only(['q', 'category_id']))
                    ->latest()
                    ->paginate(3)
            ),
        );
    }

    public function show(Event $event)
    {
        return $this->success([
            'event' => EventResource::make(
                $event->load(['category', 'tags'])
            ),
        ]);
    }
}
