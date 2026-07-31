<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . rand(100, 999),
            'category' => $this->faker->word(),
            'venue' => $this->faker->company(),
            'organizer' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->toDateTimeString(),
            'end_date' => now()->addHours(2)->toDateTimeString(),
            'status' => 'draft',
        ];
    }
}
