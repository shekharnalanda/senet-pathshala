@extends('layouts.app')
@section('title','My Results')
@section('content')
<section class="py-5 bg-light min-vh-100">
<div class="container">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">My Results</h1>
        <p class="text-secondary mb-0">{{ auth()->user()->name }} · {{ $student->admission_no }}</p>
    </div>
    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">← Dashboard</a>
</div>

@forelse($exams as $exam => $rows)
    @php
        $total = $rows->sum(fn($row) => (float) $row->max_marks);
        $obtained = $rows->sum(fn($row) => (float) $row->marks_obtained);
        $pct = $total > 0 ? round(($obtained / $total) * 100, 1) : 0;
    @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="fw-bold mb-0">{{ $exam }}</h4>
                <span class="badge bg-primary fs-6">{{ $pct }}%</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Subject</th><th>Max Marks</th><th>Obtained</th><th>Percentage</th><th>Remark</th></tr></thead>
                    <tbody>
                    @foreach($rows as $row)
                        @php
                            $maxMarks = (float) $row->max_marks;
                            $marksObtained = (float) $row->marks_obtained;
                            $sp = $maxMarks > 0 ? round(($marksObtained / $maxMarks) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td><strong>{{ $row->subject }}</strong></td>
                            <td>{{ number_format($maxMarks,2) }}</td>
                            <td>{{ number_format($marksObtained,2) }}</td>
                            <td>{{ $sp }}%</td>
                            <td>{{ $row->remark ?: '—' }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-light fw-bold"><td>Total</td><td>{{ number_format($total,2) }}</td><td>{{ number_format($obtained,2) }}</td><td>{{ $pct }}%</td><td></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@empty
    <div class="card border-0 shadow-sm"><div class="card-body text-center text-secondary py-5">No result has been published yet.</div></div>
@endforelse
</div>
</section>
@endsection
