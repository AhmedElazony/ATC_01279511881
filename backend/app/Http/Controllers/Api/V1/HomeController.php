<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\EventResource;
use App\Models\Category;
use App\Models\Event;

class HomeController extends ApiController
{
    public function __invoke()
    {
        return $this->success([
            'events' => EventResource::collection(
                Event::with(['category', 'tags'])
                    ->latest()
                    ->featured()
                    ->withBookingInfo(auth('sanctum')->user()?->id)
                    ->limit(6)
                    ->get()
            ),
            'categories' => CategoryResource::collection(
                Category::withCount('events')->limit(8)->get()
            ),
        ]);
    }
}
