<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Senet Pathshala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
    <div class="container"><a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Senet Pathshala Admin</a><a class="btn btn-outline-light btn-sm" href="{{ route('home') }}">View Website</a></div>
</nav>
<div class="container py-5">
    <div class="mb-4"><h1 class="fw-bold">Dashboard</h1><p class="text-secondary mb-0">Manage the public website from one place.</p></div>
    <div class="row g-4">
        <div class="col-md-4"><a href="{{ route('admin.notices.index') }}" class="text-decoration-none"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><i class="fa-solid fa-bullhorn fs-2 text-primary"></i><h4 class="mt-3">Notices</h4><p class="text-secondary mb-0">Create and manage school notices.</p></div></div></a></div>
        <div class="col-md-4"><a href="{{ route('admin.gallery.index') }}" class="text-decoration-none"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><i class="fa-solid fa-images fs-2 text-primary"></i><h4 class="mt-3">Gallery</h4><p class="text-secondary mb-0">Upload and manage campus photos.</p></div></div></a></div>
        <div class="col-md-4"><a href="{{ route('admin.settings.edit') }}" class="text-decoration-none"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><i class="fa-solid fa-gear fs-2 text-primary"></i><h4 class="mt-3">School Settings</h4><p class="text-secondary mb-0">Update school contact information.</p></div></div></a></div>
    </div>
</div>
</body>
</html>
