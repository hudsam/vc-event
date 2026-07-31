<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Maxy Event</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts (Outfit) -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Sidebar styling */
        #sidebar {
            width: 260px;
            background: #1e293b;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }
        
        .nav-link {
            color: #94a3b8;
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.03);
            border-left-color: #6366f1;
        }
        
        .nav-link i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }
        
        /* Topbar styling */
        #topbar {
            height: 70px;
            background: #1e293b;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 99;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }
        
        /* Content area */
        #main-content {
            margin-left: 260px;
            padding-top: 90px;
            padding-left: 2rem;
            padding-right: 2rem;
            padding-bottom: 2rem;
            min-height: calc(100vh - 70px);
        }
        
        /* Card styling */
        .card {
            background-color: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            color: #e2e8f0;
        }
        
        .card-header {
            background-color: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);
            border: none;
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        /* Table styling */
        .table {
            color: #e2e8f0;
        }
        .table > :not(caption) > * > * {
            background-color: #1e293b;
            color: #e2e8f0;
            border-bottom-color: rgba(255, 255, 255, 0.05);
            padding: 1rem;
        }
        .table tbody tr:hover td {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
    </style>
    @yield('styles')
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
    <div class="sidebar-brand">
        Maxy Event
    </div>
    <div class="nav flex-column py-3">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('admin.events.index') }}" class="nav-link {{ Route::is('admin.events.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i> Events
        </a>
    </div>
</div>

<!-- Topbar -->
<div id="topbar">
    <div class="d-flex align-items-center">
        <h5 class="mb-0 text-white">@yield('page_title', 'Dashboard')</h5>
    </div>
    <div class="d-flex align-items-center">
        <span class="me-3 text-muted"><i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}</span>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Main Content -->
<div id="main-content">
    @if(session('success'))
        <div class="alert alert-success border-0 bg-success bg-opacity-20 text-success-emphasis rounded-3 p-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 bg-danger bg-opacity-20 text-danger-emphasis rounded-3 p-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif
    
    @yield('content')
</div>

<!-- Bootstrap 5 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
