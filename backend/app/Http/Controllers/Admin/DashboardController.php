<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EventService;

class DashboardController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index()
    {
        $stats = $this->eventService->getStats();
        $latestEvents = $this->eventService->getLatest(5);
        return view('admin.dashboard', compact('stats', 'latestEvents'));
    }
}
