@extends('admin.layouts.app')
@section('title', 'Gallery')
@section('heading', 'Gallery')
@section('content')
<div class="mb-4"><h1 class="fw-bold">Gallery</h1><p class="text-secondary mb-0">Upload and manage school photographs.</p></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card border-0 shadow-sm mb-4"><div class="card-body"><h5>Upload Image</h5><form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">@csrf<div class="col-md-6"><label class="form-label">Title</label><input name="title" class="form-control" required></div><div class="col-md-6"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*" required></div><div class="col-12"><button class="btn btn-primary">Upload</button></div></form></div></div>
<div class="row g-4">@forelse($galleries as $gallery)<div class="col-md-4 col-lg-3"><div class="card border-0 shadow-sm h-100"><img src="{{ asset('storage/'.$gallery->image) }}" class="card-img-top" style="height:180px;object-fit:cover" alt="{{ $gallery->title }}"><div class="card-body"><h6>{{ $gallery->title }}</h6><form action="{{ route('admin.gallery.destroy', $gallery) }}" method="POST" onsubmit="return confirm('Delete this image?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></div></div></div>@empty<div class="col-12"><div class="alert alert-secondary">No gallery images uploaded yet.</div></div>@endforelse</div><div class="mt-4">{{ $galleries->links() }}</div>
@endsection
