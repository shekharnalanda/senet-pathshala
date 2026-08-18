@extends('layouts.app')

@section('title', 'Gallery')

@section('content')
<style>
.gallery-page-frame{height:280px;width:100%;background:#fff;display:flex;align-items:center;justify-content:center;padding:10px;overflow:hidden}
.gallery-page-img{width:100%;height:100%;object-fit:contain;object-position:center;display:block;cursor:zoom-in;border-radius:10px;transition:transform .2s ease}
.gallery-page-img:hover{transform:scale(1.015)}
.gallery-page-lightbox{position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.9);display:none;align-items:center;justify-content:center;padding:28px}
.gallery-page-lightbox.is-open{display:flex}
.gallery-page-lightbox img{max-width:95vw;max-height:88vh;width:auto;height:auto;object-fit:contain;border-radius:10px}
.gallery-page-close{position:absolute;top:18px;right:24px;width:46px;height:46px;border:0;border-radius:50%;background:#fff;font-size:28px;line-height:1}
.gallery-page-caption{position:absolute;bottom:16px;left:20px;right:20px;text-align:center;color:#fff;font-weight:600}
@media(max-width:576px){.gallery-page-frame{height:300px}}
</style>
<section class="py-5 bg-light"><div class="container"><div class="mb-5"><span class="text-primary fw-semibold">SCHOOL GALLERY</span><h1 class="display-5 fw-bold mt-2">Memories from our campus</h1><p class="text-secondary">Explore moments from school life, activities and our learning environment.</p></div><div class="row g-4">@forelse($galleries ?? [] as $gallery)<div class="col-sm-6 col-lg-4"><div class="card border-0 shadow-sm h-100 overflow-hidden"><div class="gallery-page-frame"><img src="{{ asset('storage/'.$gallery->image) }}" alt="{{ $gallery->title ?? 'School gallery' }}" class="gallery-page-img" data-full-image="{{ asset('storage/'.$gallery->image) }}" data-caption="{{ $gallery->title ?? 'School Moment' }}"></div><div class="card-body"><h5 class="mb-1">{{ $gallery->title ?? 'School Moment' }}</h5>@if(!empty($gallery->description))<p class="text-secondary small mb-0">{{ $gallery->description }}</p>@endif</div></div></div>@empty<div class="col-12"><div class="text-center py-5 border rounded-3 bg-white"><h4>No gallery images yet</h4><p class="text-secondary mb-0">School memories will appear here as images are added.</p></div></div>@endforelse</div></div></section>
<div class="gallery-page-lightbox" id="galleryPageLightbox"><button type="button" class="gallery-page-close" id="galleryPageClose">&times;</button><img id="galleryPageImage" src="" alt=""><div class="gallery-page-caption" id="galleryPageCaption"></div></div>
<script>
document.addEventListener('DOMContentLoaded',function(){const l=document.getElementById('galleryPageLightbox'),i=document.getElementById('galleryPageImage'),c=document.getElementById('galleryPageCaption'),b=document.getElementById('galleryPageClose');function close(){l.classList.remove('is-open');i.src='';document.body.style.overflow=''}document.querySelectorAll('.gallery-page-img').forEach(function(x){x.addEventListener('click',function(){i.src=x.dataset.fullImage;i.alt=x.alt||'';c.textContent=x.dataset.caption||'';l.classList.add('is-open');document.body.style.overflow='hidden'})});b.addEventListener('click',close);l.addEventListener('click',function(e){if(e.target===l)close()});document.addEventListener('keydown',function(e){if(e.key==='Escape')close()})});
</script>
@endsection
