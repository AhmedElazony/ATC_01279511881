<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends ApiController
{
    public function index()
    {
        return $this->success(
            EventResource::paginate(
                Event::with(['category', 'tags'])
                    ->latest()
                    ->filter(request()->only(['q', 'category_id']))
                    ->paginate()
            )
        );
    }

    public function store(EventRequest $request)
    {
        $validated = $request->validated();
        $imageName = $this->saveImage($validated['image']);

        if ($request->has('featured')) {
            $validated['featured'] = filter_var($validated['featured'], FILTER_VALIDATE_BOOLEAN);
        }
        $event = Event::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'date' => $validated['date'],
            'venue' => $validated['venue'],
            'image_path' => "events/{$imageName}",
            'featured' => $validated['featured'] ?? false,
        ]);

        if ($request->has('tags_array')) {
            $this->handleTags($request, $event);
        }

        return $this->success([
            'message' => 'Event created successfully',
            'event' => EventResource::make($event->load(['category', 'tags'])),
        ], 201);
    }

    public function update(EventRequest $request, Event $event)
    {
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($event->image_path);
            $imageName = $this->saveImage($validated['image']);
            $event->image_path = "events/{$imageName}";
        }
        if ($request->has('featured')) {
            $event->featured = filter_var($validated['featured'], FILTER_VALIDATE_BOOLEAN);
        }
        $event->update([
            'name' => $validated['name'] ?? $event->name,
            'description' => $validated['description'] ?? $event->description,
            'category_id' => $validated['category_id'] ?? $event->category_id,
            'price' => $validated['price'] ?? $event->price,
            'date' => $validated['date'] ?? $event->date,
            'venue' => $validated['venue'] ?? $event->venue,
            'featured' => $validated['featured'] ?? $event->featured,
        ]);

        if ($request->has('tags_array')) {
            $this->handleTags($request, $event);
        }

        return $this->success([
            'message' => 'Event updated successfully',
            'event' => EventResource::make($event->load(['category', 'tags'])),
        ]);
    }

    public function destroy(Event $event)
    {
        Storage::disk('public')->delete($event->image_path);
        $event->tags()->detach();
        $event->delete();

        return $this->success('Event deleted successfully');
    }

    protected function saveImage($image)
    {
        $imageName = now()->format('d-m-Y-H-i-s') . '-' . uniqid() . $image->getClientOriginalName();
        Storage::disk('public')->putFileAs(
            'events',
            $image,
            $imageName
        );

        return $imageName;
    }

    protected function handleTags(Request $request, Event $event)
    {
        $tagsToAttach = [];
        foreach ($request->tags_array as $tagName) {
            // Find or create the tag
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagsToAttach[] = $tag->id;
        }

        // Attach tags to the event
        $event->tags()->sync($tagsToAttach);
    }
}
