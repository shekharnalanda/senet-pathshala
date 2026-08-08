<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>School Settings | Senet Pathshala Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Senet Pathshala Admin</a></div></nav>
<div class="container py-5">
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1>School Settings</h1><p class="text-secondary mb-0">Manage the basic information shown across the website.</p></div><a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card border-0 shadow-sm"><div class="card-body p-4"><form action="{{ route('admin.settings.update') }}" method="POST">@csrf @method('PUT')
<div class="row g-4"><div class="col-md-6"><label class="form-label">School Name</label><input name="school_name" value="{{ old('school_name', $settings->school_name) }}" class="form-control" required></div><div class="col-md-6"><label class="form-label">Phone</label><input name="phone" value="{{ old('phone', $settings->phone) }}" class="form-control"></div><div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $settings->email) }}" class="form-control"></div><div class="col-md-6"><label class="form-label">Website</label><input type="url" name="website" value="{{ old('website', $settings->website) }}" class="form-control"></div><div class="col-12"><label class="form-label">Address</label><textarea name="address" rows="4" class="form-control">{{ old('address', $settings->address) }}</textarea></div><div class="col-12"><button class="btn btn-primary px-4">Save Settings</button></div></div>
</form></div></div></div></body></html>
