@extends('layouts.admin')

@section('title', 'Events')
@section('page_title', 'Event Management')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <!-- Status filter links -->
        <div class="btn-group" role="group">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-light {{ is_null($status) ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.events.index', ['status' => 'draft']) }}" class="btn btn-outline-light {{ $status === 'draft' ? 'active' : '' }}">Draft</a>
            <a href="{{ route('admin.events.index', ['status' => 'published']) }}" class="btn btn-outline-light {{ $status === 'published' ? 'active' : '' }}">Published</a>
            <a href="{{ route('admin.events.index', ['status' => 'archived']) }}" class="btn btn-outline-light {{ $status === 'archived' ? 'active' : '' }}">Archived</a>
        </div>
        
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Event
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
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
                            @forelse ($events as $event)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($event->thumbnail)
                                                <img src="{{ $event->thumbnail }}" alt="{{ $event->title }}" class="rounded-3 me-3" style="width: 55px; height: 55px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
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
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-eye"></i> Show
                                            </a>
                                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No events found matching this status.</td>
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
