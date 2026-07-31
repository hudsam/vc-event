<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EventService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.api.url');
    }

    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    protected function hydrateModel(array $data)
    {
        $event = new Event();
        $event->setRawAttributes($data, true);
        return $event;
    }

    protected function hydrateCollection(array $data)
    {
        return collect($data)->map(function ($item) {
            return $this->hydrateModel($item);
        });
    }

    public function getAll($status = null)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->apiUrl . '/events', [
                'status' => $status,
            ]);

        if ($response->successful()) {
            return $this->hydrateCollection($response->json('data') ?? []);
        }

        return collect();
    }

    public function getLatest($limit = 5)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->apiUrl . '/events');

        if ($response->successful()) {
            $events = $this->hydrateCollection($response->json('data') ?? []);
            return $events->take($limit);
        }

        return collect();
    }

    public function find($idOrSlug)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->apiUrl . '/events/' . $idOrSlug);

        if ($response->successful()) {
            return $this->hydrateModel($response->json('data'));
        }

        return null;
    }

    public function create(array $data)
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->apiUrl . '/events', $data);

        if ($response->successful()) {
            return $this->hydrateModel($response->json('data'));
        }

        throw new \Exception($response->json('message', 'Failed to create event.'));
    }

    public function update(Event $event, array $data)
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $response = Http::withHeaders($this->getHeaders())
            ->put($this->apiUrl . '/events/' . $event->id, $data);

        if ($response->successful()) {
            return $this->hydrateModel($response->json('data'));
        }

        throw new \Exception($response->json('message', 'Failed to update event.'));
    }

    public function delete(Event $event)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->delete($this->apiUrl . '/events/' . $event->id);

        if ($response->successful()) {
            return true;
        }

        throw new \Exception($response->json('message', 'Failed to delete event.'));
    }

    public function changeStatus(Event $event, string $status)
    {
        if (!in_array($status, ['draft', 'published', 'archived'])) {
            throw new \InvalidArgumentException('Invalid status');
        }

        if ($status === 'published') {
            if (empty($event->title) || empty($event->category) || empty($event->venue) || empty($event->start_date) || empty($event->thumbnail) || empty($event->banner)) {
                throw new \Exception('Event must have all required fields before publishing.');
            }
        }

        $data = $event->toArray();
        $data['status'] = $status;

        $response = Http::withHeaders($this->getHeaders())
            ->put($this->apiUrl . '/events/' . $event->id, $data);

        if ($response->successful()) {
            return $this->hydrateModel($response->json('data'));
        }

        throw new \Exception($response->json('message', 'Failed to change event status.'));
    }

    public function getStats()
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->apiUrl . '/events');

        if ($response->successful()) {
            $events = collect($response->json('data') ?? []);
            return [
                'total' => $events->count(),
                'draft' => $events->where('status', 'draft')->count(),
                'published' => $events->where('status', 'published')->count(),
                'archived' => $events->where('status', 'archived')->count(),
            ];
        }

        return [
            'total' => 0,
            'draft' => 0,
            'published' => 0,
            'archived' => 0,
        ];
    }
}

