<?php

namespace App\Http\Resources;

use Api\Support\Traits\ApiResource\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    use WithPagination;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => EventResource::make($this->whenLoaded('event')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'total_price' => $this->total_price,
            // 'status' => $this->status,
            'booked_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
