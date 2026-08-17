@extends('layouts.app')

@section('title', 'Digital ID Card')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold mb-1">Digital ID Card</h1>
                <p class="text-secondary mb-0">C-Net Pathshala</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">Back to Dashboard</a>
        </div>

        <div class="card border-0 shadow mx-auto" style="max-width: 720px;">
            <div class="card-header bg-primary text-white text-center py-3">
                <h2 class="h4 fw-bold mb-0">C-Net Pathshala</h2>
                <div class="small">STUDENT IDENTITY CARD</div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-sm-4 text-center">
                        @if($student->photo)
                            <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->user->name }}" class="img-fluid rounded border" style="width:140px;height:170px;object-fit:cover;">
                        @else
                            <div class="border rounded bg-light d-flex align-items-center justify-content-center mx-auto" style="width:140px;height:170px;">
                                <span class="text-secondary">Student Photo</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        <h3 class="h4 fw-bold mb-3">{{ $student->user->name }}</h3>
                        <p class="mb-2"><strong>Admission No.:</strong> {{ $student->admission_no }}</p>
                        <p class="mb-2"><strong>Class / Section:</strong> {{ $student->class_name ?: '—' }} {{ $student->section ? '/ '.$student->section : '' }}</p>
                        <p class="mb-2"><strong>Roll No.:</strong> {{ $student->roll_no ?: '—' }}</p>
                        <p class="mb-2"><strong>Date of Birth:</strong> {{ $student->date_of_birth ? $student->date_of_birth->format('d-m-Y') : '—' }}</p>
                        <p class="mb-0"><strong>Guardian:</strong> {{ $student->guardian_name ?: '—' }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center bg-white py-3">
                <span class="badge bg-success">ACTIVE STUDENT</span>
                <p class="small text-secondary mb-0 mt-2">Admission No. {{ $student->admission_no }}</p>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" class="btn btn-primary" onclick="window.print()">Print ID Card</button>
        </div>
    </div>
</section>
@endsection
