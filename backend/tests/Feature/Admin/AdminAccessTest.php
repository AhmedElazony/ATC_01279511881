<?php
// filepath: /home/ahmedelazony/develop/competitions/areeb/backend/tests/Feature/Admin/AdminAccessTest.php

namespace Tests\Feature\Admin;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_endpoints()
    {
        $admin = User::factory()->create([
            'role' => RolesEnum::ADMIN->value,
        ]);

        $response = $this->actingAs($admin)
            ->getJson('/api/v1/admin/events');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_admin_endpoints()
    {
        $superAdmin = User::factory()->create([
            'role' => RolesEnum::SUPER_ADMIN->value,
        ]);

        $response = $this->actingAs($superAdmin)
            ->getJson('/api/v1/admin/events');

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_admin_endpoints()
    {
        $user = User::factory()->create([
            'role' => RolesEnum::USER->value,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/admin/events');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_endpoints()
    {
        $response = $this->getJson('/api/v1/admin/events');

        $response->assertStatus(401);
    }
}
