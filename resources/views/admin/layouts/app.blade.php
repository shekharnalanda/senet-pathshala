<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>@yield('title','Admin') | C-Net Pathshala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <style>
        body{overflow-x:hidden}.admin-sidebar{width:278px;min-width:278px;background:#162033!important}.admin-main{min-width:0}.admin-brand{padding:.35rem .4rem}.admin-group{margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid rgba(255,255,255,.08)}.admin-group:last-child{border-bottom:0}.admin-group-title{font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;color:#9da9ba;font-weight:800;padding:.35rem .75rem .45rem}.admin-nav-link{border-radius:.65rem;color:#eef3f8!important;padding:.62rem .75rem;display:flex;align-items:center;gap:.65rem}.admin-nav-link i{width:1.15rem;text-align:center;color:#b7c7dc}.admin-nav-link:hover,.admin-nav-link.active{background:rgba(255,255,255,.1)}.admin-nav-link.active i{color:#fff}.admin-content{max-width:1600px}.admin-user-chip{font-size:.8rem}.admin-sidebar-footer{position:sticky;bottom:0;background:#162033;padding-top:.65rem}
        @media(max-width:991.98px){.admin-sidebar{width:84px;min-width:84px;padding:.75rem!important}.admin-sidebar .brand-text,.admin-sidebar .admin-group-title,.admin-sidebar .nav-text,.admin-sidebar .website-text{display:none}.admin-sidebar .admin-group{margin-bottom:.55rem;padding-bottom:.4rem}.admin-sidebar .admin-nav-link{justify-content:center;padding:.72rem .3rem}.admin-sidebar .admin-nav-link i{width:auto;font-size:1.05rem}.admin-content{padding:1rem!important}}
    </style>
</head>
<body class="bg-light">
@php($u = auth()->user())
<div class="d-flex min-vh-100">
    <aside class="admin-sidebar text-white p-3">
        <a href="{{ $u->is_admin ? route('admin.dashboard') : '#' }}" class="admin-brand text-white text-decoration-none fs-5 fw-bold d-block mb-3">
            <i class="fa-solid fa-graduation-cap me-2"></i><span class="brand-text">C-Net Pathshala</span>
        </a>

        <div class="small text-secondary mb-3 px-2 admin-user-chip">
            <span class="brand-text">{{ $u->is_admin ? 'Administrator' : ucwords(str_replace('_',' ',$u->role)).' Panel' }}</span>
        </div>

        <nav class="nav flex-column">
            @if($u->is_admin)
                <div class="admin-group">
                    <div class="admin-group-title">Overview</div>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-gauge"></i><span class="nav-text">Dashboard</span>
                    </a>
                </div>
            @endif

            <div class="admin-group">
                <div class="admin-group-title">Academic Setup</div>
                @if($u->is_admin)
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}" href="{{ route('admin.branches.index') }}">
                        <i class="fa-solid fa-building-columns"></i><span class="nav-text">Campuses</span>
                    </a>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.staff-users.*') ? 'active' : '' }}" href="{{ route('admin.staff-users.index') }}">
                        <i class="fa-solid fa-users-gear"></i><span class="nav-text">Staff Users</span>
                    </a>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}" href="{{ route('admin.classes.index') }}">
                        <i class="fa-solid fa-school"></i><span class="nav-text">Classes & Sections</span>
                    </a>
                @endif
                @if($u->hasPermission('students'))
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                        <i class="fa-solid fa-user-graduate"></i><span class="nav-text">Students</span>
                    </a>
                @endif
            </div>

            <div class="admin-group">
                <div class="admin-group-title">Daily Operations</div>
                @if($u->hasPermission('attendance'))
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" href="{{ route('admin.attendance.index') }}">
                        <i class="fa-solid fa-calendar-check"></i><span class="nav-text">Attendance</span>
                    </a>
                @endif
                @if($u->hasPermission('homework'))
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.homework.*') ? 'active' : '' }}" href="{{ route('admin.homework.index') }}">
                        <i class="fa-solid fa-book-open"></i><span class="nav-text">Homework</span>
                    </a>
                @endif
                @if($u->hasPermission('results') || $u->hasPermission('report_cards'))
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}" href="{{ route('admin.results.index') }}">
                        <i class="fa-solid fa-square-poll-vertical"></i><span class="nav-text">Results</span>
                    </a>
                @endif
                @if($u->hasPermission('fees'))
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}" href="{{ route('admin.fees.index') }}">
                        <i class="fa-solid fa-indian-rupee-sign"></i><span class="nav-text">Fees</span>
                    </a>
                @endif
            </div>

            <div class="admin-group">
                <div class="admin-group-title">Cards & Documents</div>
                @if($u->is_admin)
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.student-id-cards.*') ? 'active' : '' }}" href="{{ route('admin.student-id-cards.index') }}">
                        <i class="fa-solid fa-id-card"></i><span class="nav-text">ID Cards</span>
                    </a>
                @endif
                @if($u->hasPermission('certificates'))
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}" href="{{ route('admin.documents.index') }}">
                        <i class="fa-solid fa-file-lines"></i><span class="nav-text">Certificates</span>
                    </a>
                @endif
            </div>

            @if($u->is_admin)
                <div class="admin-group">
                    <div class="admin-group-title">Website & Admissions</div>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.admission-applications.*') ? 'active' : '' }}" href="{{ route('admin.admission-applications.index') }}">
                        <i class="fa-solid fa-file-signature"></i><span class="nav-text">Online Admissions</span>
                    </a>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}" href="{{ route('admin.notices.index') }}">
                        <i class="fa-solid fa-bullhorn"></i><span class="nav-text">Notices</span>
                    </a>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" href="{{ route('admin.gallery.index') }}">
                        <i class="fa-solid fa-images"></i><span class="nav-text">Gallery</span>
                    </a>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}" href="{{ route('admin.programs.index') }}">
                        <i class="fa-solid fa-shapes"></i><span class="nav-text">Learning Programs</span>
                    </a>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}" href="{{ route('admin.contacts.index') }}">
                        <i class="fa-solid fa-envelope"></i><span class="nav-text">Enquiries</span>
                    </a>
                    <a class="admin-nav-link nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
                        <i class="fa-solid fa-gear"></i><span class="nav-text">Settings</span>
                    </a>
                </div>
            @endif
        </nav>

        <div class="admin-sidebar-footer">
            <a href="{{ route('home') }}" class="admin-nav-link nav-link">
                <i class="fa-solid fa-globe"></i><span class="website-text">View Website</span>
            </a>
        </div>
    </aside>

    <main class="admin-main flex-grow-1">
        <header class="bg-white border-bottom px-3 px-md-4 py-3 d-flex justify-content-between align-items-center gap-3">
            <div>
                <strong>@yield('heading', $u->is_admin ? 'Dashboard' : 'Staff Panel')</strong>
                @if(!$u->is_admin)
                    <small class="text-secondary ms-2">{{ ucwords(str_replace('_',' ',$u->role)) }}</small>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </header>
        <div class="admin-content p-4 p-lg-5 mx-auto">
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
