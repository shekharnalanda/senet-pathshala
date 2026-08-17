<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | C-Net Pathshala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        body { overflow-x: hidden; }
        .admin-sidebar { width: 260px; min-width: 260px; }
        .admin-nav-link { border-radius: .6rem; }
        .admin-nav-link:hover, .admin-nav-link.active { background: rgba(255,255,255,.1); }
        @media (max-width: 767.98px) {
            .admin-sidebar { width: 72px; min-width: 72px; padding: .65rem !important; }
            .admin-sidebar .brand-text, .admin-sidebar .section-label, .admin-sidebar .nav-text, .admin-sidebar .website-text { display: none; }
            .admin-sidebar .nav-link { text-align: center; padding: .75rem .25rem; }
            .admin-sidebar .nav-link i { margin-right: 0 !important; }
            .admin-content { padding: 1rem !important; }
        }
    </style>
</head>
<body class="bg-light">
<div class="d-flex min-vh-100">
    <aside class="admin-sidebar bg-dark text-white p-3">
        <a href="{{ route('admin.dashboard') }}" class="brand-link text-white text-decoration-none fs-5 fw-bold d-block mb-4">
            <span class="brand-text">C-Net Pathshala</span>
        </a>

        <div class="section-label small text-uppercase text-secondary mb-2">Management</div>

        @php
            $links = [
                ['admin.dashboard', 'admin.dashboard', 'fa-gauge', 'Dashboard'],
                ['admin.students.index', 'admin.students.*', 'fa-user-graduate', 'Students'],
                ['admin.attendance.index', 'admin.attendance.*', 'fa-calendar-check', 'Attendance'],
                ['admin.homework.index', 'admin.homework.*', 'fa-book-open', 'Homework'],
                ['admin.results.index', 'admin.results.*', 'fa-square-poll-vertical', 'Results'],
                ['admin.fees.index', 'admin.fees.*', 'fa-indian-rupee-sign', 'Fees'],
                ['admin.student-id-cards.index', 'admin.student-id-cards.*', 'fa-id-card', 'ID Cards'],
                ['admin.notices.index', 'admin.notices.*', 'fa-bullhorn', 'Notices'],
                ['admin.gallery.index', 'admin.gallery.*', 'fa-images', 'Gallery'],
                ['admin.contacts.index', 'admin.contacts.*', 'fa-envelope', 'Enquiries'],
                ['admin.settings.edit', 'admin.settings.*', 'fa-gear', 'Settings'],
            ];
        @endphp

        <nav class="nav flex-column gap-1">
            @foreach ($links as [$route, $match, $icon, $label])
                <a class="admin-nav-link nav-link text-white {{ request()->routeIs($match) ? 'active' : '' }}" href="{{ route($route) }}">
                    <i class="fa-solid {{ $icon }} me-2"></i>
                    <span class="nav-text">{{ $label }}</span>
                </a>
            @endforeach
        </nav>

        <hr class="border-secondary">
        <a href="{{ route('home') }}" class="admin-nav-link nav-link text-white">
            <i class="fa-solid fa-globe me-2"></i><span class="website-text">View Website</span>
        </a>
    </aside>

    <main class="admin-main flex-grow-1 min-w-0">
        <header class="bg-white border-bottom px-3 px-md-4 py-3 d-flex justify-content-between align-items-center">
            <strong>@yield('heading', 'Dashboard')</strong>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </header>
        <div class="admin-content p-4 p-lg-5">@yield('content')</div>
    </main>
</div>
</body>
</html>
