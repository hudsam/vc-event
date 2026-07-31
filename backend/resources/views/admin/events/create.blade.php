@extends('layouts.admin')

@section('title', 'Create Event')
@section('page_title', 'Create New Event')

@section('content')
<div class="row">
    <div class="col-12 col-xl-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white">Event Information</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-20 text-danger-emphasis rounded-3 p-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.events.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Event Title</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Jakarta Tech Conference 2026">
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control bg-dark border-secondary text-white" id="slug" name="slug" value="{{ old('slug') }}" required placeholder="e.g. jakarta-tech-conference-2026">
                        <small class="text-muted">Will be automatically generated if left empty.</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select bg-dark border-secondary text-white" id="category" name="category" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat['name'] }}" {{ old('category') === $cat['name'] ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="venue" class="form-label">Venue</label>
                            <select class="form-select bg-dark border-secondary text-white" id="venue" name="venue" required>
                                <option value="">Select Venue</option>
                                @foreach ($venues as $v)
                                    <option value="{{ $v['name'] }}" {{ old('venue') === $v['name'] ? 'selected' : '' }}>{{ $v['name'] }} ({{ $v['city'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="organizer" class="form-label">Organizer</label>
                            <select class="form-select bg-dark border-secondary text-white" id="organizer" name="organizer" required>
                                <option value="">Select Organizer</option>
                                @foreach ($organizers as $o)
                                    <option value="{{ $o['name'] }}" {{ old('organizer') === $o['name'] ? 'selected' : '' }}>{{ $o['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date & Time</label>
                            <input type="datetime-local" class="form-control bg-dark border-secondary text-white" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date & Time</label>
                            <input type="datetime-local" class="form-control bg-dark border-secondary text-white" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control bg-dark border-secondary text-white" id="description" name="description" rows="5" placeholder="Write full details about the event...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Unsplash Image URLs -->
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail URL (Unsplash)</label>
                        <input type="url" class="form-control bg-dark border-secondary text-white image-preview-input" id="thumbnail" name="thumbnail" value="{{ old('thumbnail', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400&h=400&fit=crop&q=80') }}" required placeholder="https://images.unsplash.com/photo-...">
                    </div>

                    <div class="mb-4">
                        <label for="banner" class="form-label">Banner URL (Unsplash)</label>
                        <input type="url" class="form-control bg-dark border-secondary text-white image-preview-input" id="banner" name="banner" value="{{ old('banner', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&h=500&fit=crop&q=80') }}" required placeholder="https://images.unsplash.com/photo-...">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-light">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Image Preview Sidebar -->
    <div class="col-12 col-xl-4">
        <div class="card mb-4 position-sticky" style="top: 90px;">
            <div class="card-header">
                <h5 class="mb-0 text-white">Live Image Preview</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Thumbnail Preview</label>
                    <div class="bg-secondary bg-opacity-10 border border-secondary border-opacity-20 rounded-3 overflow-hidden d-flex align-items-center justify-content-center" style="height: 180px;">
                        <img id="thumbnail-preview" src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400&h=400&fit=crop&q=80" alt="Thumbnail Preview" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
                <div>
                    <label class="form-label text-muted">Banner Preview</label>
                    <div class="bg-secondary bg-opacity-10 border border-secondary border-opacity-20 rounded-3 overflow-hidden d-flex align-items-center justify-content-center" style="height: 120px;">
                        <img id="banner-preview" src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&h=500&fit=crop&q=80" alt="Banner Preview" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Slug automatic generation
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    titleInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.value === slugify(titleInput.defaultValue)) {
            slugInput.value = slugify(titleInput.value);
        }
    });
    
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start
            .replace(/-+$/, '');            // Trim - from end
    }

    // Image Live Preview
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailPreview = document.getElementById('thumbnail-preview');
    const bannerInput = document.getElementById('banner');
    const bannerPreview = document.getElementById('banner-preview');

    thumbnailInput.addEventListener('input', function() {
        if (thumbnailInput.value) {
            thumbnailPreview.src = thumbnailInput.value;
        }
    });

    bannerInput.addEventListener('input', function() {
        if (bannerInput.value) {
            bannerPreview.src = bannerInput.value;
        }
    });
</script>
@endsection
