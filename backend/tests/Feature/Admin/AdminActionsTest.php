<?php

namespace Tests\Feature\Admin;

use App\Enums\RolesEnum;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_event()
    {
        $admin = User::factory()->create([
            'role' => RolesEnum::ADMIN->value,
        ]);

        $eventData = [
            'name' => 'New Event',
            'description' => 'Event Description',
            'date' => '2023-10-01',
            'venue' => 'Event Location',
            'price' => 100,
            'image' => UploadedFile::fake()->image('event.jpg'),
            'category_id' => Category::factory()->create()->id,
        ];

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/admin/events/store', $eventData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'event' => [
                        'id',
                        'name',
                        'description',
                        'date',
                        'venue',
                        'price',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('events', [
            'name' => $eventData['name'],
            'description' => $eventData['description'],
            'date' => $eventData['date'],
            'venue' => $eventData['venue'],
            'price' => $eventData['price'],
        ]);
    }
    public function test_admin_can_update_event()
    {
        $admin = User::factory()->create([
            'role' => RolesEnum::ADMIN->value,
        ]);

        $event = Event::factory()->create();

        $updatedData = [
            'name' => 'Updated Event',
            'description' => 'Updated Description',
            'date' => '2023-11-01',
            'venue' => 'Updated Location',
            'price' => 150,
        ];

        $response = $this->actingAs($admin)
            ->putJson("/api/v1/admin/events/{$event->id}/update", $updatedData);

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
                    ]
                ]
            ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => $updatedData['name'],
            'description' => $updatedData['description'],
            'date' => $updatedData['date'],
            'venue' => $updatedData['venue'],
            'price' => $updatedData['price'],
        ]);
    }
    public function test_admin_can_delete_event()
    {
        $admin = User::factory()->create([
            'role' => RolesEnum::ADMIN->value,
        ]);

        $event = Event::factory()->withFeaturedImage()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/v1/admin/events/{$event->id}/destroy");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }

    public function test_super_admin_can_add_admin()
    {
        $admin = User::factory()->create([
            'role' => RolesEnum::SUPER_ADMIN->value,
        ]);

        $userData = [
            'name' => 'New User',
            'email' => 'hello@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => RolesEnum::ADMIN->value,
        ];
        $response = $this->actingAs($admin)
            ->postJson('/api/v1/admin/users', $userData);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ]
                ]
            ]);
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => $userData['role'],
        ]);
    }
}
