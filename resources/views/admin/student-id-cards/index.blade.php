@extends('admin.layouts.app')
@section('title', 'Bulk ID Cards')
@section('heading', 'Bulk ID Cards')
@section('content')
<div class="mb-4"><h1 class="fw-bold">Bulk ID Card Print</h1><p class="text-secondary mb-0">Select exactly 2 active students to print on one A4 portrait sheet.</p></div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.student-id-cards.print') }}" target="_blank">@csrf
<div class="card border-0 shadow-sm"><div class="card-body p-4">
<div class="table-responsive"><table class="table align-middle"><thead><tr><th style="width:60px">Select</th><th>Student</th><th>Admission No.</th><th>Class / Section</th><th>Roll No.</th></tr></thead><tbody>
@forelse($students as $student)<tr><td><input class="form-check-input student-check" type="checkbox" name="students[]" value="{{ $student->id }}"></td><td class="fw-semibold">{{ $student->user->name }}</td><td>{{ $student->admission_no }}</td><td>{{ $student->class_name ?: '—' }} {{ $student->section ? '/ '.$student->section : '' }}</td><td>{{ $student->roll_no ?: '—' }}</td></tr>@empty<tr><td colspan="5" class="text-center text-secondary py-4">No active students found.</td></tr>@endforelse
</tbody></table></div>
<div class="d-flex justify-content-between align-items-center mt-3"><span id="selected-count" class="text-secondary">0 of 2 selected</span><button id="print-btn" class="btn btn-primary" type="submit" disabled>Print 2 Students</button></div>
</div></div></form>
<script>document.addEventListener('DOMContentLoaded',function(){const checks=[...document.querySelectorAll('.student-check')],count=document.getElementById('selected-count'),btn=document.getElementById('print-btn');function sync(){const n=checks.filter(x=>x.checked).length;count.textContent=n+' of 2 selected';btn.disabled=n!==2;checks.forEach(x=>x.disabled=!x.checked&&n>=2)}checks.forEach(x=>x.addEventListener('change',sync));sync();});</script>
@endsection
