<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | Senet Pathshala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="d-flex min-vh-100">
    <aside class="bg-dark text-white p-3" style="width:260px">
        <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fs-5 fw-bold d-block mb-4">Senet Pathshala</a>
        <div class="small text-uppercase text-secondary mb-2">Management</div>
        <nav class="nav flex-column gap-1">
            <a class="nav-link text-white" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a>
            <a class="nav-link text-white" href="{{ route('admin.notices.index') }}"><i class="fa-solid fa-bullhorn me-2"></i>Notices</a>
            <a class="nav-link text-white" href="{{ route('admin.gallery.index') }}"><i class="fa-solid fa-images me-2"></i>Gallery</a>
            <a class="nav-link text-white" href="{{ route('admin.contacts.index') }}"><i class="fa-solid fa-envelope me-2"></i>Enquiries</a>
            <a class="nav-link text-white" href="{{ route('admin.settings.edit') }}"><i class="fa-solid fa-gear me-2"></i>Settings</a>
        </nav>
        <hr class="border-secondary">
        <a href="{{ route('home') }}" class="nav-link text-white"><i class="fa-solid fa-globe me-2"></i>View Website</a>
    </aside>
    <main class="flex-grow-1">
        <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
            <strong>@yield('heading', 'Dashboard')</strong>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="btn btn-sm btn-outline-danger">Logout</button></form>
        </header>
        <div class="p-4 p-lg-5">@yield('content')</div>
    </main>
</div>
</body>
</html>
