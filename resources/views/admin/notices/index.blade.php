<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Notices | Senet Pathshala Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Senet Pathshala Admin</a></div></nav>
<div class="container py-5">
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1>Notices</h1><p class="text-secondary mb-0">Publish and manage school announcements.</p></div><a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Dashboard</a></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm mb-4"><div class="card-body"><h5>Add Notice</h5><form action="{{ route('admin.notices.store') }}" method="POST" class="row g-3">@csrf<div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" required></div><div class="col-md-3"><label class="form-label">Date</label><input type="date" name="notice_date" class="form-control" required></div><div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="1">Published</option><option value="0">Draft</option></select></div><div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control"></textarea></div><div class="col-12"><button class="btn btn-primary">Publish Notice</button></div></form></div></div>
<div class="card border-0 shadow-sm"><div class="card-body"><h5 class="mb-3">Existing Notices</h5><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Title</th><th>Date</th><th>Status</th><th class="text-end">Action</th></tr></thead><tbody>@forelse($notices as $notice)<tr><td><strong>{{ $notice->title }}</strong><div class="small text-secondary">{{ $notice->description }}</div></td><td>{{ $notice->notice_date?->format('d M Y') }}</td><td><span class="badge {{ $notice->status ? 'bg-success' : 'bg-secondary' }}">{{ $notice->status ? 'Published' : 'Draft' }}</span></td><td class="text-end"><form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" onsubmit="return confirm('Delete this notice?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-4">No notices yet.</td></tr>@endforelse</tbody></table></div>{{ $notices->links() }}</div></div>
</div></body></html>
