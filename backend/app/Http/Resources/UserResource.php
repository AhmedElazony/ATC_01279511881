<?php

namespace App\Http\Resources;

use Api\Support\Traits\ApiResource\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'role' => $this->role,
            'registered_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

