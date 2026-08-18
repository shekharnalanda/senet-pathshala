@extends('admin.layouts.app')
@section('title','Learning Programs')
@section('heading','Learning Programs CMS')
@section('content')
<div class="mb-4"><h1 class="fw-bold">Learning Programs</h1><p class="text-secondary">Edit Play Group, Nursery, LKG and UKG content shown on the website.</p></div>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
@foreach($programs as $program)
<form action="{{ route('admin.programs.update',$program) }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm mb-4">@csrf @method('PUT')
<div class="card-body p-4"><div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">{{ $program->name }}</h4><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($program->is_active)><label class="form-check-label">Show on website</label></div></div><div class="row g-3">
<div class="col-md-6"><label class="form-label">Program Name</label><input class="form-control" name="name" value="{{ old('name',$program->name) }}" required></div><div class="col-md-6"><label class="form-label">Program Image</label><input class="form-control" type="file" name="image" accept="image/*"></div>
<div class="col-12"><label class="form-label">Home Card Short Text</label><textarea class="form-control" name="card_text" rows="2">{{ old('card_text',$program->card_text) }}</textarea></div><div class="col-12"><label class="form-label">Detail Page Introduction</label><textarea class="form-control" name="intro" rows="3" required>{{ old('intro',$program->intro) }}</textarea></div>
<div class="col-12"><label class="form-label">What Children Learn</label><textarea class="form-control" name="focus" rows="6">{{ old('focus',$program->focus) }}</textarea><div class="form-text">हर learning point नई line में लिखें.</div></div><div class="col-12"><label class="form-label">Learning Approach</label><textarea class="form-control" name="learning_approach" rows="4">{{ old('learning_approach',$program->learning_approach) }}</textarea></div>
<div class="col-12"><button class="btn btn-primary">Save {{ $program->name }}</button></div></div></div></form>
@endforeach
@endsection