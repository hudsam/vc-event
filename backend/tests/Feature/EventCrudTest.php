<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'admin@maxy.academy'
        ]);
    }

    public function test_can_view_events_list(): void
    {
        Event::create([
            'title' => 'Test Event 1',
            'slug' => 'test-event-1',
            'category' => 'Technology',
            'venue' => 'Jakarta Convention Center',
            'organizer' => 'Maxy Academy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now(),
            'end_date' => now()->addHours(2),
        ]);

        $response = $this->actingAs($this->user)->get('/admin/events');

        $response->assertStatus(200);
        $response->assertSee('Test Event 1');
    }

    public function test_can_create_event(): void
    {
        $eventData = [
            'title' => 'New Event',
            'slug' => 'new-event',
            'category' => 'Marketing',
            'venue' => 'Online (Zoom)',
            'organizer' => 'Maxy Academy',
            'description' => 'Cool description',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addHours(3)->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($this->user)->post('/admin/events', $eventData);

        $response->assertRedirect('/admin/events');
        $this->assertDatabaseHas('events', [
            'title' => 'New Event',
            'slug' => 'new-event',
        ]);
    }

    public function test_can_update_event(): void
    {
        $event = Event::create([
            'title' => 'Old Event',
            'slug' => 'old-event',
            'category' => 'Technology',
            'venue' => 'Jakarta Convention Center',
            'organizer' => 'Maxy Academy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now(),
            'end_date' => now()->addHours(2),
        ]);

        $response = $this->actingAs($this->user)->put("/admin/events/{$event->id}", [
            'title' => 'Updated Event Title',
            'slug' => 'updated-event-title',
            'category' => 'Technology',
            'venue' => 'Jakarta Convention Center',
            'organizer' => 'Maxy Academy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addHours(2)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect('/admin/events');
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Event Title',
        ]);
    }

    public function test_can_delete_event(): void
    {
        $event = Event::create([
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'category' => 'Technology',
            'venue' => 'Jakarta Convention Center',
            'organizer' => 'Maxy Academy',
            'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
            'start_date' => now(),
            'end_date' => now()->addHours(2),
        ]);

        $response = $this->actingAs($this->user)->delete("/admin/events/{$event->id}");

        $response->assertRedirect('/admin/events');
        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
    }
}
