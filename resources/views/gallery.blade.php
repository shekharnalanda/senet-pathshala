@extends('layouts.app')

@section('title', 'Gallery')

@section('content')
<section class="py-5 bg-light"><div class="container"><div class="mb-5"><span class="text-primary fw-semibold">SCHOOL GALLERY</span><h1 class="display-5 fw-bold mt-2">Memories from our campus</h1><p class="text-secondary">Explore moments from school life, activities and our learning environment.</p></div><div class="row g-4">@forelse($galleries ?? [] as $gallery)<div class="col-sm-6 col-lg-4"><div class="card border-0 shadow-sm h-100 overflow-hidden"><img src="{{ asset('storage/'.$gallery->image) }}" alt="{{ $gallery->title ?? 'School gallery' }}" class="w-100" style="height:240px;object-fit:cover"><div class="card-body"><h5 class="mb-1">{{ $gallery->title ?? 'School Moment' }}</h5>@if(!empty($gallery->description))<p class="text-secondary small mb-0">{{ $gallery->description }}</p>@endif</div></div></div>@empty<div class="col-12"><div class="text-center py-5 border rounded-3 bg-white"><h4>No gallery images yet</h4><p class="text-secondary mb-0">School memories will appear here as images are added.</p></div></div>@endforelse</div></div></section>
@endsection
