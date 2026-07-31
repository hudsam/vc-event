<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('event');
        $eventId = $event ? (is_object($event) ? $event->id : $event) : null;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:events,slug,' . $eventId,
            'category' => 'required|string',
            'venue' => 'required|string',
            'organizer' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'required|url',
            'banner' => 'required|url',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:draft,published,archived',
        ];
    }
}
