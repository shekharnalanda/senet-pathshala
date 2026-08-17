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
            .admin-sidebar { width: 72px; min-width: 72px; padding-left: .65rem !important; padding-right: .65rem !important; }
            .admin-sidebar .brand-text, .admin-sidebar .section-label, .admin-sidebar .nav-text, .admin-sidebar .website-text { display: none; }
            .admin-sidebar .nav-link { text-align: center; padding: .75rem .25rem; }
            .admin-sidebar .nav-link i { margin-right: 0 !important; font-size: 1.05rem; }
            .admin-sidebar .brand-link { text-align: center; }
            .admin-content { padding: 1rem !important; }
        }
    </style>
</head>
<body class="bg-light">
<div class="d-flex min-vh-100">
    <aside class="admin-sidebar bg-dark text-white p-3">
        <a href="{{ route('admin.dashboard') }}" class="brand-link text-white text-decoration-none fs-5 fw-bold d-block mb-4"><span class="brand-text">C-Net Pathshala</span><span class="d-none d-md-inline">CP</span></a>
        <div class="section-label small text-uppercase text-secondary mb-2">Management</div>
        <nav class="nav flex-column gap-1">
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge me-2"></i><span class="nav-text">Dashboard</span></a>
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}"><i class="fa-solid fa-user-graduate me-2"></i><span class="nav-text">Students</span></a>
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}" href="{{ route('admin.fees.index') }}"><i class="fa-solid fa-indian-rupee-sign me-2"></i><span class="nav-text">Fees</span></a>
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.student-id-cards.*') ? 'active' : '' }}" href="{{ route('admin.student-id-cards.index') }}"><i class="fa-solid fa-id-card me-2"></i><span class="nav-text">ID Cards</span></a>
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}" href="{{ route('admin.notices.index') }}"><i class="fa-solid fa-bullhorn me-2"></i><span class="nav-text">Notices</span></a>
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" href="{{ route('admin.gallery.index') }}"><i class="fa-solid fa-images me-2"></i><span class="nav-text">Gallery</span></a>
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}"><i class="fa-solid fa-envelope me-2"></i><span class="nav-text">Enquiries</span></a>
            <a class="admin-nav-link nav-link text-white {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><i class="fa-solid fa-gear me-2"></i><span class="nav-text">Settings</span></a>
        </nav>
        <hr class="border-secondary">
        <a href="{{ route('home') }}" class="admin-nav-link nav-link text-white"><i class="fa-solid fa-globe me-2"></i><span class="website-text">View Website</span></a>
    </aside>
    <main class="admin-main flex-grow-1 min-w-0">
        <header class="bg-white border-bottom px-3 px-md-4 py-3 d-flex justify-content-between align-items-center">
            <strong>@yield('heading', 'Dashboard')</strong>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button></form>
        </header>
        <div class="admin-content p-4 p-lg-5">@yield('content')</div>
    </main>
</div>
</body>
</html>
