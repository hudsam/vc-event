@extends('layouts.admin')

@section('title', $event->title)
@section('page_title', 'Event Details')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-light btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Events
        </a>
        
        <!-- Status Action Buttons -->
        <div class="d-inline-flex gap-2">
            @if ($event->status !== 'draft')
                <form action="{{ route('admin.events.draft', $event) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-file-earmark-text"></i> Revert to Draft
                    </button>
                </form>
            @endif
            
            @if ($event->status !== 'published')
                <form action="{{ route('admin.events.publish', $event) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check-circle"></i> Publish Event
                    </button>
                </form>
            @endif

            @if ($event->status !== 'archived')
                <form action="{{ route('admin.events.archive', $event) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-archive"></i> Archive Event
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil"></i> Edit Details
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Event Details -->
    <div class="col-12 col-xl-8">
        <div class="card mb-4 overflow-hidden">
            @if ($event->banner)
                <img src="{{ $event->banner }}" alt="{{ $event->title }} Banner" class="img-fluid" style="width: 100%; height: 320px; object-fit: cover;">
            @else
                <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 320px;">
                    <i class="bi bi-image text-muted fs-1"></i>
                </div>
            @endif
            
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2">{{ $event->category }}</span>
                    @if ($event->status === 'published')
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Published</span>
                    @elseif ($event->status === 'archived')
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Archived</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Draft</span>
                    @endif
                </div>
                
                <h2 class="text-white mb-3 font-weight-bold">{{ $event->title }}</h2>
                <p class="text-muted"><i class="bi bi-calendar-range me-2"></i> {{ $event->start_date ? $event->start_date->format('l, d F Y - H:i') : '-' }} to {{ $event->end_date ? $event->end_date->format('l, d F Y - H:i') : '-' }}</p>
                <p class="text-muted"><i class="bi bi-geo-alt me-2"></i> {{ $event->venue }}</p>
                <p class="text-muted"><i class="bi bi-building me-2"></i> Organized by {{ $event->organizer }}</p>
                
                <hr class="border-secondary my-4">
                
                <h5 class="text-white mb-3">Description</h5>
                <div class="text-secondary" style="white-space: pre-wrap; line-height: 1.6;">{!! nl2br(e($event->description ?: 'No description provided.')) !!}</div>
            </div>
        </div>

        <!-- Static Dummy Related Data Tabs -->
        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs border-0" id="eventTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 px-4 rounded-0 border-0" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule-pane" type="button" role="tab" aria-controls="schedule-pane" aria-selected="true"><i class="bi bi-clock me-1"></i> Schedules</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 rounded-0 border-0" id="speakers-tab" data-bs-toggle="tab" data-bs-target="#speakers-pane" type="button" role="tab" aria-controls="speakers-pane" aria-selected="false"><i class="bi bi-people me-1"></i> Speakers</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 rounded-0 border-0" id="sponsors-tab" data-bs-toggle="tab" data-bs-target="#sponsors-pane" type="button" role="tab" aria-controls="sponsors-pane" aria-selected="false"><i class="bi bi-award me-1"></i> Sponsors</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 rounded-0 border-0" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery-pane" type="button" role="tab" aria-controls="gallery-pane" aria-selected="false"><i class="bi bi-images me-1"></i> Gallery</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 rounded-0 border-0" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq-pane" type="button" role="tab" aria-controls="faq-pane" aria-selected="false"><i class="bi bi-question-circle me-1"></i> FAQ</button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="eventTabsContent">
                    
                    <!-- Schedules Pane -->
                    <div class="tab-pane fade show active" id="schedule-pane" role="tabpanel" aria-labelledby="schedule-tab" tabindex="0">
                        <div class="position-relative border-start border-secondary border-opacity-50 ps-4 ms-2">
                            @foreach ($schedules as $sched)
                                <div class="mb-4 position-relative">
                                    <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: -31px; top: 6px; border: 2px solid #1e293b;"></div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0 text-white font-weight-bold">{{ $sched['title'] }}</h6>
                                        <span class="badge bg-secondary bg-opacity-20 text-muted">{{ $sched['start_time'] }} - {{ $sched['end_time'] }}</span>
                                    </div>
                                    <p class="text-muted small mb-0">Location: {{ $event->venue }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Speakers Pane -->
                    <div class="tab-pane fade" id="speakers-pane" role="tabpanel" aria-labelledby="speakers-tab" tabindex="0">
                        <div class="row g-4">
                            @foreach ($speakers as $spk)
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center bg-dark bg-opacity-30 border border-secondary border-opacity-10 rounded-3 p-3 h-100">
                                        <img src="{{ $spk['photo'] }}" alt="{{ $spk['name'] }}" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 text-white font-weight-bold">{{ $spk['name'] }}</h6>
                                            <small class="text-primary d-block mb-1">{{ $spk['title'] }}</small>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ Str::limit($spk['bio'], 70) }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Sponsors Pane -->
                    <div class="tab-pane fade" id="sponsors-pane" role="tabpanel" aria-labelledby="sponsors-tab" tabindex="0">
                        <div class="row g-4 align-items-center">
                            @foreach ($sponsors as $spon)
                                <div class="col-6 col-sm-4 col-md-3 text-center">
                                    <div class="bg-dark bg-opacity-20 border border-secondary border-opacity-15 rounded-3 p-3">
                                        <img src="{{ $spon['logo'] }}" alt="{{ $spon['name'] }}" class="img-fluid mb-2 rounded-2" style="max-height: 45px; object-fit: contain;">
                                        <h6 class="mb-1 text-white font-weight-bold" style="font-size: 0.85rem;">{{ $spon['name'] }}</h6>
                                        <span class="badge text-uppercase bg-secondary bg-opacity-20 text-muted" style="font-size: 0.65rem;">{{ str_replace('_', ' ', $spon['tier']) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Gallery Pane -->
                    <div class="tab-pane fade" id="gallery-pane" role="tabpanel" aria-labelledby="gallery-tab" tabindex="0">
                        <div class="row g-3">
                            @foreach ($galleries as $gal)
                                <div class="col-6 col-md-3">
                                    <div class="rounded-3 overflow-hidden" style="height: 140px;">
                                        <img src="{{ $gal }}" alt="Event Gallery Image" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- FAQ Pane -->
                    <div class="tab-pane fade" id="faq-pane" role="tabpanel" aria-labelledby="faq-tab" tabindex="0">
                        <div class="accordion accordion-flush" id="faqAccordion">
                            @foreach ($faqs as $i => $faq)
                                <div class="accordion-item bg-transparent border-bottom border-secondary border-opacity-10 py-2">
                                    <h2 class="accordion-header bg-transparent" id="flush-heading{{ $i }}">
                                        <button class="accordion-button collapsed bg-transparent text-white border-0 shadow-none px-0" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{ $i }}" aria-expanded="false" aria-controls="flush-collapse{{ $i }}">
                                            {{ $faq['question'] }}
                                        </button>
                                    </h2>
                                    <div id="flush-collapse{{ $i }}" class="accordion-collapse collapse" aria-labelledby="flush-heading{{ $i }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-secondary px-0 pb-1">
                                            {{ $faq['answer'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Thumbnail Sidebar -->
    <div class="col-12 col-xl-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white">Event Thumbnail</h5>
            </div>
            <div class="card-body text-center">
                @if ($event->thumbnail)
                    <img src="{{ $event->thumbnail }}" alt="{{ $event->title }} Thumbnail" class="rounded-3 img-fluid border border-secondary border-opacity-25" style="max-height: 240px; object-fit: cover;">
                @else
                    <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center rounded-3 mx-auto" style="width: 200px; height: 200px;">
                        <i class="bi bi-image text-muted fs-1"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
