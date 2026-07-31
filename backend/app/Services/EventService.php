<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Str;

class EventService
{
    public function getAll($status = null)
    {
        $query = Event::query();
        if ($status) {
            $query->where('status', $status);
        }
        return $query->orderBy('start_date', 'desc')->get();
    }

    public function getLatest($limit = 5)
    {
        return Event::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function create(array $data)
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }
        return Event::create($data);
    }

    public function update(Event $event, array $data)
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        $event->update($data);
        return $event;
    }

    public function delete(Event $event)
    {
        return $event->delete();
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

        $event->status = $status;
        $event->save();
        return $event;
    }

    public function getStats()
    {
        return [
            'total' => Event::count(),
            'draft' => Event::where('status', 'draft')->count(),
            'published' => Event::where('status', 'published')->count(),
            'archived' => Event::where('status', 'archived')->count(),
        ];
    }
}
