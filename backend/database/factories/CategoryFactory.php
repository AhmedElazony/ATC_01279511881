<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Entertainment' => '🎭',
            'Sports' => '🏆',
            'Food & Drink' => '🍽️',
            'Business' => '💼',
            'Education' => '🎓',
            'Travel' => '✈️',
            'Music' => '🎵',
            'Technology' => '💻',
            'Health' => '🧘',
            'Fashion' => '👗',
            'Art' => '🎨',
            'Gaming' => '🎮',
            'Networking' => '🤝',
            'Workshop' => '🛠️',
            'Outdoors' => '🏞️',
            'Science' => '🔬',
        ];

        $name = $this->faker->unique()->randomElement(array_keys($categories));
        $icon = $categories[$name];

        return [
            'name' => $name,
            'icon' => $icon,
        ];
    }
}
