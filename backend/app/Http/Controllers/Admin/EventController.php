<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status');
        $events = $this->eventService->getAll($status);
        return view('admin.events.index', compact('events', 'status'));
    }

    public function create()
    {
        $categories = config('dummy.categories');
        $venues = config('dummy.venues');
        $organizers = config('dummy.organizers');
        return view('admin.events.create', compact('categories', 'venues', 'organizers'));
    }

    public function store(EventRequest $request)
    {
        $this->eventService->create($request->validated());
        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $speakers = config('dummy.speakers');
        $sponsors = config('dummy.sponsors');
        $schedules = config('dummy.schedules');
        $galleries = config('dummy.galleries');
        $faqs = config('dummy.faqs');

        return view('admin.events.show', compact('event', 'speakers', 'sponsors', 'schedules', 'galleries', 'faqs'));
    }

    public function edit(Event $event)
    {
        $categories = config('dummy.categories');
        $venues = config('dummy.venues');
        $organizers = config('dummy.organizers');
        return view('admin.events.edit', compact('event', 'categories', 'venues', 'organizers'));
    }

    public function update(EventRequest $request, Event $event)
    {
        $this->eventService->update($event, $request->validated());
        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $this->eventService->delete($event);
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }

    public function publish(Event $event)
    {
        try {
            $this->eventService->changeStatus($event, 'published');
            return redirect()->back()->with('success', 'Event published successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function archive(Event $event)
    {
        try {
            $this->eventService->changeStatus($event, 'archived');
            return redirect()->back()->with('success', 'Event archived successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function draft(Event $event)
    {
        try {
            $this->eventService->changeStatus($event, 'draft');
            return redirect()->back()->with('success', 'Event reverted to draft successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
