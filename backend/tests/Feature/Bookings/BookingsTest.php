<?php

namespace Tests\Feature\Bookings;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_book_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['price' => 50]);

        $response = $this->actingAs($user)
            ->postJson("/api/v1/events/{$event->id}/book");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'booking' => [
                        'id',
                        'user',
                        'event',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_price' => 50,
        ]);
    }

    public function test_guest_cannot_book_event()
    {
        $event = Event::factory()->create();

        $response = $this->postJson("/api/v1/events/{$event->id}/book");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_fetch_their_bookings()
    {
        $user = User::factory()->create();
        $events = Event::factory(3)->create();

        // Create some bookings for the user
        foreach ($events as $event) {
            Booking::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'total_price' => $event->price,
            ]);
        }

        $response = $this->actingAs($user)
            ->getJson("/api/v1/events/booked");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'event',
                        'total_price',
                        'booked_at',
                    ]
                ]
            ]);

        $this->assertCount(3, $response['data']);
    }

    public function test_user_cannot_view_other_users_bookings()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $event = Event::factory()->create();

        // Create a booking for user1
        Booking::create([
            'user_id' => $user1->id,
            'event_id' => $event->id,
            'total_price' => $event->price,
        ]);

        // User2 tries to access bookings
        $response = $this->actingAs($user2)
            ->getJson("/api/v1/events/booked");

        $response->assertStatus(200);
        $this->assertEmpty($response['data']); // User2 should see their own bookings (none)
    }
}
