@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="text-primary fw-semibold">ABOUT OUR SCHOOL</span>
                <h1 class="display-5 fw-bold mt-2">A place to learn, grow and shine.</h1>
                <p class="lead text-secondary">We are committed to providing a safe, supportive and inspiring learning environment where every child can build strong foundations for the future.</p>
                <p class="text-secondary">Our approach combines academic learning with values, creativity, confidence and practical skills. We encourage students to stay curious, respect others and discover their individual strengths.</p>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold">Our Mission</h4>
                        <p class="text-secondary mb-0">To nurture responsible, confident and capable learners through quality education, caring mentorship and meaningful opportunities.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4"><div class="h-100 p-4 border rounded-3"><h4>Academic Excellence</h4><p class="text-secondary mb-0">Strong fundamentals, thoughtful teaching and continuous encouragement help students progress with confidence.</p></div></div>
            <div class="col-md-4"><div class="h-100 p-4 border rounded-3"><h4>Character & Values</h4><p class="text-secondary mb-0">We promote discipline, kindness, responsibility, teamwork and respect in everyday school life.</p></div></div>
            <div class="col-md-4"><div class="h-100 p-4 border rounded-3"><h4>All-Round Growth</h4><p class="text-secondary mb-0">Activities, creativity and practical experiences help children develop beyond the classroom.</p></div></div>
        </div>
    </div>
</section>
@endsection
