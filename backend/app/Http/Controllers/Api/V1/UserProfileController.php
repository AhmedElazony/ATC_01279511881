<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Resources\UserResource;

class UserProfileController extends ApiController
{
    public function __invoke()
    {
        $user = auth()->user();
        return $this->success(
            [
                'total_bookings' => $user->bookings()->count(),
                'upcoming_events' => $user->bookings()
                    ->with('event')
                    ->whereHas('event', function ($query) {
                        $query->upcoming();
                    })
                    ->latest()
                    ->get()
                    ->count(),
            ]
        );
    }
}
