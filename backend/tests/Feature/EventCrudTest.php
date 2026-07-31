<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
        Http::fake([
            '*/api/events*' => Http::response([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 1,
                        'title' => 'Test Event 1',
                        'slug' => 'test-event-1',
                        'category' => 'Technology',
                        'venue' => 'Jakarta Convention Center',
                        'organizer' => 'Maxy Academy',
                        'description' => 'Test description',
                        'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
                        'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
                        'start_date' => now()->toDateTimeString(),
                        'end_date' => now()->addHours(2)->toDateTimeString(),
                        'status' => 'draft',
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->user)->get('/admin/events');

        $response->assertStatus(200);
        $response->assertSee('Test Event 1');
    }

    public function test_can_create_event(): void
    {
        Http::fake([
            '*/api/events' => Http::response([
                'status' => 'success',
                'data' => [
                    'id' => 1,
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
                    'status' => 'draft',
                ]
            ], 201)
        ]);

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

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
            return $request->url() == 'http://127.0.0.1:8000/api/events' &&
                   $request->method() == 'POST' &&
                   $request['title'] == 'New Event';
        });
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

        Http::fake([
            '*/api/events/*' => Http::response([
                'status' => 'success',
                'data' => [
                    'id' => $event->id,
                    'title' => 'Updated Event Title',
                    'slug' => 'updated-event-title',
                    'category' => 'Technology',
                    'venue' => 'Jakarta Convention Center',
                    'organizer' => 'Maxy Academy',
                    'thumbnail' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
                    'banner' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87',
                    'start_date' => now()->format('Y-m-d H:i:s'),
                    'end_date' => now()->addHours(2)->format('Y-m-d H:i:s'),
                    'status' => 'draft',
                ]
            ], 200)
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

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) use ($event) {
            return $request->url() == 'http://127.0.0.1:8000/api/events/' . $event->id &&
                   $request->method() == 'PUT' &&
                   $request['title'] == 'Updated Event Title';
        });
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

        Http::fake([
            '*/api/events/*' => Http::response([
                'status' => 'success',
                'message' => 'Event deleted successfully'
            ], 200)
        ]);

        $response = $this->actingAs($this->user)->delete("/admin/events/{$event->id}");

        $response->assertRedirect('/admin/events');

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) use ($event) {
            return $request->url() == 'http://127.0.0.1:8000/api/events/' . $event->id &&
                   $request->method() == 'DELETE';
        });
    }
}

