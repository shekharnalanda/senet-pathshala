@include('layouts.header')

@if(isset($branches) && $branches->count())
<div class="bg-light border-bottom">
    <div class="container py-2">
        <form method="GET" action="{{ route('home') }}" class="d-flex flex-wrap align-items-center justify-content-center gap-2">
            <span class="fw-semibold text-secondary me-1"><i class="fa-solid fa-location-dot me-1"></i>Campus:</span>
            <select name="branch_id" class="form-select form-select-sm" style="max-width:260px" onchange="this.form.submit()">
                <option value="">All Campuses</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" @selected((string)($selectedBranchId ?? '') === (string)$branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
            @if(!empty($selectedBranch))
                <span class="small text-secondary">Showing notices, programs and gallery for {{ $selectedBranch->name }} plus school-wide items.</span>
            @endif
        </form>
    </div>
</div>
@endif

@yield('content')

@include('layouts.footer')
