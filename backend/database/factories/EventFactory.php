<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'venue' => $this->faker->address,
            'price' => $this->faker->randomFloat(2, 0, 100),
            'category_id' => \App\Models\Category::factory(),
            'featured' => $this->faker->boolean(20), // 20% chance to be featured
        ];
    }

    public function withFeaturedImage(): self
    {
        return $this->afterCreating(function (Event $event) {
            // Get a random image from Unsplash
            $imageUrl = 'https://picsum.photos/800/600';

            // Create temp file
            $tempFile = tempnam(sys_get_temp_dir(), 'post_image_');
            file_put_contents($tempFile, file_get_contents($imageUrl));

            // Store the image in the public directory
            $imagePath = 'images/' . uniqid() . '.jpg';
            \Storage::disk('public')->put($imagePath, file_get_contents($tempFile));
            $event->image_path = $imagePath;
            $event->save();

            // Remove temp file
            @unlink($tempFile);
        });
    }

}
