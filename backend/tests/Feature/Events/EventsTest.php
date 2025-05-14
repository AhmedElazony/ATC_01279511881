<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_all_events()
    {
        // Create some test data
        $category = Category::factory()->create();
        Event::factory(3)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'events' => [
                        'items',
                        'paginate',
                    ],
                    'categories',
                ]
            ]);

        $this->assertCount(3, $response['data']['events']['items']);
    }

    public function test_can_filter_events_by_category()
    {
        // Create categories and events
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $event1 = Event::factory()->create(['category_id' => $category1->id]);
        $event2 = Event::factory()->create(['category_id' => $category2->id]);

        $response = $this->getJson("/api/v1/events?category_id={$category1->id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response['data']['events']['items']);
        $this->assertEquals($event1->id, $response['data']['events']['items'][0]['id']);
    }

    public function test_can_search_events()
    {
        // Create events with specific names
        Event::factory()->create(['name' => 'Tech Conference 2023']);
        Event::factory()->create(['name' => 'Music Festival']);

        $response = $this->getJson("/api/v1/events?q=Tech");

        $response->assertStatus(200);
        $this->assertCount(1, $response['data']['events']['items']);
        $this->assertEquals('Tech Conference 2023', $response['data']['events']['items'][0]['name']);
    }

    public function test_can_view_single_event()
    {
        $category = Category::factory()->create();
        $tags = Tag::factory(3)->create();
        $event = Event::factory()->create(['category_id' => $category->id]);
        $event->tags()->attach($tags->pluck('id')->toArray());

        $response = $this->getJson("/api/v1/events/{$event->id}/show");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'event' => [
                        'id',
                        'name',
                        'description',
                        'date',
                        'venue',
                        'price',
                        'image_url',
                        'category',
                        'tags',
                    ]
                ]
            ]);

        $this->assertEquals($event->id, $response['data']['event']['id']);
        $this->assertCount(3, $response['data']['event']['tags']);
    }
}
