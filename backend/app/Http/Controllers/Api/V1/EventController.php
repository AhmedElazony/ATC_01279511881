<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Actions\GetAllEventsWithBookingInfoAction;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventController extends ApiController
{
    public function index()
    {
        $events = (new GetAllEventsWithBookingInfoAction(
            auth('sanctum')->user()?->id
        ))->execute();

        return $this->success(EventResource::paginate($events));
    }

    public function show(string $eventId)
    {
        return $this->success([
            'event' => EventResource::make(
                Event::with(['category', 'tags'])
                    ->withBookingInfo(auth('sanctum')->user()?->id)
                    ->findOrFail($eventId)
            ),
        ]);
    }
}
