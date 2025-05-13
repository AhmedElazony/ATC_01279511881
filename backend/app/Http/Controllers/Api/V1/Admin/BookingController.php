<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Resources\BookingResource;
use App\Models\Booking;

class BookingController extends ApiController
{
    public function __invoke()
    {
        return $this->success(
            BookingResource::paginate(
                Booking::with(['event.category', 'event.tags', 'user'])
                    ->latest()
                    ->paginate()
            )
        );
    }
}
