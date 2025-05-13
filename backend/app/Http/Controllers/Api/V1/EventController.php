<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Actions\GetAllEventsWithBookingInfoAction;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\TagResource;
use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class EventController extends ApiController
{
    public function index()
    {
        $events = (new GetAllEventsWithBookingInfoAction(
            auth('sanctum')->user()?->id,
            request()->only(['q', 'category_id'])
        ))->execute();

        $categories = Category::withCount('events')->get();

        return $this->success([
            'events' => EventResource::paginate($events),
            'categories' => CategoryResource::collection($categories),
        ]);
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
