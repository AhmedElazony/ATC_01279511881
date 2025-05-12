<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\Event;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Super Admin
        User::updateOrCreate([
            'email' => 'super@admin.com',
        ], [
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password@admin'),
            'role' => RolesEnum::SUPER_ADMIN->value,
        ]);

        // Seed Events.
        $categories = \App\Models\Category::factory(10)->create();
        $tags = \App\Models\Tag::factory(20)->create();
        Event::factory(10)
            ->recycle($categories)
            ->withFeaturedImage()
            ->create()
            ->each(function ($event) use ($tags) {
                $event->tags()->attach($tags->random(rand(2, 5))->pluck('id')->toArray());
            });
    }
}
