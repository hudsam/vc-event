@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Cards -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-2 font-weight-bold" style="font-size: 0.8rem;">Total Events</h6>
                    <h3 class="mb-0 text-white font-weight-bold">{{ $stats['total'] }}</h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-calendar-event fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-2 font-weight-bold" style="font-size: 0.8rem;">Drafts</h6>
                    <h3 class="mb-0 text-warning font-weight-bold">{{ $stats['draft'] }}</h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-file-earmark-text fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-2 font-weight-bold" style="font-size: 0.8rem;">Published</h6>
                    <h3 class="mb-0 text-success font-weight-bold">{{ $stats['published'] }}</h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-check-circle fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase mb-2 font-weight-bold" style="font-size: 0.8rem;">Archived</h6>
                    <h3 class="mb-0 text-danger font-weight-bold">{{ $stats['archived'] }}</h3>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                    <i class="bi bi-archive fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-white">Latest Events</h5>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Add Event
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Event Details</th>
                                <th>Category</th>
                                <th>Venue</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestEvents as $event)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($event->thumbnail)
                                                <img src="{{ $event->thumbnail }}" alt="{{ $event->title }}" class="rounded-3 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 text-white font-weight-bold">{{ $event->title }}</h6>
                                                <small class="text-muted">By {{ $event->organizer }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $event->category }}</td>
                                    <td>{{ $event->venue }}</td>
                                    <td>{{ $event->start_date ? $event->start_date->format('M d, Y H:i') : '-' }}</td>
                                    <td>
                                        @if ($event->status === 'published')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Published</span>
                                        @elseif ($event->status === 'archived')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Archived</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-info btn-sm me-1">
                                            <i class="bi bi-eye"></i> Show
                                        </a>
                                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No events found. Add your first event!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
