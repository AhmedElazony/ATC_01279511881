<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Resources\BookingResource;
use App\Models\Event;

class BookingController extends ApiController
{
    public function store(Event $event)
    {
        $user = auth()->user();
        $booking = $user->bookings()->create([
            'event_id' => $event->id,
            'total_price' => $event->price,
        ]);
        return $this->success([
            'message' => 'Booking created successfully',
            'booking' => BookingResource::make($booking->load(['event', 'user'])),
        ]);
    }

    public function index()
    {
        $user = auth()->user();
        return $this->success(
            BookingResource::collection(
                $user->bookings()
                    ->with(['event.category', 'user'])
                    ->latest()
                    ->get()
            ),
        );
    }
}
