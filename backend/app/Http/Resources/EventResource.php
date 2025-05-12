<?php

namespace App\Http\Resources;

use Api\Support\Traits\ApiResource\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'description' => $this->description,
            'date' => $this->date,
            'venue' => $this->venue,
            'price' => $this->price,
            'image_url' => asset("storage/{$this->image_path}"),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'added_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
