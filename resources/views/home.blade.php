@extends('layouts.master')

@section('title', 'Senet Pathshala | Home')

@section('content')
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 mb-3">Admission Open 2026-27</span>
            <h1>Welcome to Senet Pathshala</h1>
            <p class="lead">A caring, safe and joyful foundation for your child's early years.</p>
            <p>Play Group &nbsp;|&nbsp; Nursery &nbsp;|&nbsp; LKG &nbsp;|&nbsp; UKG</p>
            <div class="mt-4 d-flex flex-wrap gap-3">
                <a href="{{ url('/admission') }}" class="btn btn-school">Apply Online</a>
                <a href="{{ url('/contact') }}" class="btn btn-light rounded-pill px-4 fw-semibold">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="section-kicker">Welcome</span>
                <h2 class="display-6 fw-bold mt-2">Learning today, leading tomorrow.</h2>
                <p class="text-secondary mt-3">Senet Pathshala is committed to building a strong foundation through joyful learning, caring teachers, positive values and age-appropriate activities.</p>
                <div class="row g-3 mt-2">
                    <div class="col-sm-6"><div class="program-card text-start"><i class="fa-solid fa-shield-heart text-primary fs-3 mb-2"></i><h5>Safe Environment</h5><p class="mb-0 text-secondary">A secure and caring campus for children.</p></div></div>
                    <div class="col-sm-6"><div class="program-card text-start"><i class="fa-solid fa-lightbulb text-primary fs-3 mb-2"></i><h5>Joyful Learning</h5><p class="mb-0 text-secondary">Activities that make learning meaningful.</p></div></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="program-card p-2">
                    <img src="{{ asset('images/school-building.jpg') }}" class="img-fluid rounded-4 w-100" alt="Senet Pathshala campus">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-lg-3">
        <div class="text-center mb-5">
            <span class="section-kicker">Programs</span>
            <h2 class="display-6 fw-bold mt-2">Our Learning Programs</h2>
            <p class="text-secondary">Age-appropriate learning experiences for every early stage.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['Play Group','Learning through fun, play and social activities.','playgroup.png'],
                ['Nursery','Building language, motor and basic learning skills.','nursery.png'],
                ['LKG','Developing confidence and strong foundational concepts.','lkg.png'],
                ['UKG','Preparing children for a confident transition to school.','ukg.png']
            ] as $program)
                <div class="col-lg-3 col-md-6">
                    <div class="program-card">
                        <img src="{{ asset('images/'.$program[2]) }}" class="img-fluid mb-3" alt="{{ $program[0] }}">
                        <h4>{{ $program[0] }}</h4>
                        <p class="text-secondary mb-0">{{ $program[1] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-lg-3">
        <div class="text-center mb-5">
            <span class="section-kicker">Why Us</span>
            <h2 class="display-6 fw-bold mt-2">Why Choose Senet Pathshala?</h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['fa-chalkboard-user','Qualified Teachers','Caring educators focused on each child’s development.'],
                ['fa-shield-halved','Safe Campus','A secure environment designed around children's wellbeing.'],
                ['fa-computer','Smart Learning','Technology and activities used to support classroom learning.'],
                ['fa-person-running','Activities & Play','Learning through movement, creativity and participation.'],
                ['fa-book-open','Reading Foundation','Early language and reading habits encouraged from the start.'],
                ['fa-people-group','Parent Partnership','Regular communication to keep parents connected.']
            ] as $feature)
                <div class="col-lg-4 col-md-6">
                    <div class="program-card text-start">
                        <i class="fa-solid {{ $feature[0] }} text-primary fs-3 mb-3"></i>
                        <h5>{{ $feature[1] }}</h5>
                        <p class="text-secondary mb-0">{{ $feature[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark rounded-pill mb-2">Session 2026-27</span>
                <h2 class="display-6 fw-bold">Admissions are now open.</h2>
                <p class="mb-0 opacity-75">Take the first step toward a happy and confident learning journey.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ url('/admission') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-bold">Apply Now</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-lg-3">
        <div class="row g-5">
            <div class="col-lg-7">
                <span class="section-kicker">Updates</span>
                <h2 class="display-6 fw-bold mt-2 mb-4">Latest Notices</h2>
                <div class="list-group shadow-sm rounded-4 overflow-hidden">
                    <a href="#" class="list-group-item list-group-item-action p-4">Admission Open for Session 2026-27 <span class="badge bg-primary float-end">New</span></a>
                    <a href="#" class="list-group-item list-group-item-action p-4">Parent-Teacher Meeting <span class="text-secondary float-end">10 Aug 2026</span></a>
                    <a href="#" class="list-group-item list-group-item-action p-4">Independence Day Celebration <span class="text-secondary float-end">15 Aug 2026</span></a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="program-card text-start h-100">
                    <span class="section-kicker">Quick Information</span>
                    <h4 class="mt-2">School Office</h4>
                    <p class="mb-2"><strong>School Time:</strong> 08:00 AM – 01:30 PM</p>
                    <p class="mb-2"><strong>Office:</strong> 09:00 AM – 04:00 PM</p>
                    <p class="mb-2"><strong>Phone:</strong> 7004773247</p>
                    <p class="mb-0"><strong>Email:</strong> cnetbiharsharif@gmail.com</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-lg-3">
        <div class="text-center mb-5">
            <span class="section-kicker">Campus Life</span>
            <h2 class="display-6 fw-bold mt-2">School Gallery</h2>
        </div>
        <div class="row g-4">
            @for($i = 1; $i <= 4; $i++)
                <div class="col-lg-3 col-md-6">
                    <img src="{{ asset('images/gallery'.$i.'.jpg') }}" class="img-fluid rounded-4 shadow-sm w-100" alt="Senet Pathshala activity {{ $i }}">
                </div>
            @endfor
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-lg-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-4 text-center">
                <img src="{{ asset('images/principal.jpg') }}" class="img-fluid rounded-circle shadow-sm" alt="Principal">
            </div>
            <div class="col-lg-8">
                <span class="section-kicker">Principal's Message</span>
                <h2 class="display-6 fw-bold mt-2">Growing curious, kind and confident learners.</h2>
                <p class="text-secondary mt-3">We believe every child deserves an environment where they feel safe to explore, ask questions and discover their strengths. Our focus is to make the early years joyful while building strong habits, values and foundational skills.</p>
                <p class="fw-bold mb-0">Principal, Senet Pathshala</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container py-lg-3">
        <div class="text-center mb-5">
            <span class="section-kicker">Get In Touch</span>
            <h2 class="display-6 fw-bold mt-2">Contact Us</h2>
        </div>
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="program-card text-start">
                    <h4 class="mb-4">Send an Enquiry</h4>
                    <form action="#" method="post">
                        @csrf
                        <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Mobile Number</label><input type="tel" name="mobile" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Message</label><textarea name="message" rows="4" class="form-control"></textarea></div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Send Enquiry</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="program-card text-start h-100">
                    <h4>Main Campus</h4>
                    <p>Opp. Kalawati Palace, Quamruddin Ganj, Bihar Sharif, Nalanda - 803101</p>
                    <h5 class="mt-4">Branch Office</h5>
                    <p>At + PO Goraur, PS Chhabilapur, Nalanda - 803116</p>
                    <p class="mb-1"><strong>Phone:</strong> 7004773247</p>
                    <p><strong>Email:</strong> cnetbiharsharif@gmail.com</p>
                    <div class="ratio ratio-16x9 mt-4 rounded-4 overflow-hidden">
                        <iframe src="https://www.google.com/maps?q=Bihar+Sharif&output=embed" title="Senet Pathshala location map" loading="lazy" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
