<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_events_list(): void
    {
        Event::factory()->create([
            'title' => 'Sample Conference',
            'slug' => 'sample-conference',
            'category' => 'Technology',
            'venue' => 'JCC',
            'organizer' => 'Maxy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->toDateTimeString(),
            'end_date' => now()->addHours(2)->toDateTimeString(),
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id', 'title', 'slug', 'category', 'venue', 'organizer', 'thumbnail', 'banner', 'status'
                    ]
                ]
            ])
            ->assertJsonFragment(['title' => 'Sample Conference']);
    }

    public function test_can_get_single_event(): void
    {
        $event = Event::factory()->create([
            'title' => 'Single Event',
            'slug' => 'single-event',
            'category' => 'Technology',
            'venue' => 'JCC',
            'organizer' => 'Maxy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->toDateTimeString(),
            'end_date' => now()->addHours(2)->toDateTimeString(),
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/events/' . $event->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Single Event');

        // Also test fetch by slug
        $responseSlug = $this->getJson('/api/events/single-event');
        $responseSlug->assertStatus(200)
            ->assertJsonPath('data.id', $event->id);
    }

    public function test_can_create_event(): void
    {
        $data = [
            'title' => 'API Created Event',
            'slug' => 'api-created-event',
            'category' => 'Business',
            'venue' => 'Zoom',
            'organizer' => 'Maxy Academy',
            'description' => 'Test API creation description.',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addHours(3)->format('Y-m-d H:i:s'),
            'status' => 'draft',
        ];

        $response = $this->postJson('/api/events', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'API Created Event');

        $this->assertDatabaseHas('events', [
            'title' => 'API Created Event',
            'slug' => 'api-created-event',
        ]);
    }

    public function test_can_update_event(): void
    {
        $event = Event::factory()->create([
            'title' => 'Before Update',
            'slug' => 'before-update',
            'category' => 'Technology',
            'venue' => 'JCC',
            'organizer' => 'Maxy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'status' => 'draft',
        ]);

        $data = [
            'title' => 'After Update',
            'slug' => 'after-update',
            'category' => 'Technology',
            'venue' => 'JCC',
            'organizer' => 'Maxy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'status' => 'published',
        ];

        $response = $this->putJson('/api/events/' . $event->id, $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'After Update');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'After Update',
        ]);
    }

    public function test_can_delete_event(): void
    {
        $event = Event::factory()->create([
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'category' => 'Technology',
            'venue' => 'JCC',
            'organizer' => 'Maxy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now(),
            'end_date' => now()->addHours(2),
        ]);

        $response = $this->deleteJson('/api/events/' . $event->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Event deleted successfully']);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }
}
