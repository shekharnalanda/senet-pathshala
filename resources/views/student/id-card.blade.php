@extends('layouts.app')

@section('title', 'Digital ID Card')

@section('content')
<style>
.student-id-wrap{display:flex;justify-content:center}.student-id-card{width:360px;overflow:hidden;border-radius:20px;background:#fff;border:1px solid #dbe6f2;box-shadow:0 18px 45px rgba(8,47,103,.18);color:#142b47}.id-topbar{position:relative;padding:18px 20px 26px;background:linear-gradient(120deg,#082f67,#0757a4 68%,#118de0);color:#fff}.id-topbar:after{content:'';position:absolute;left:-5%;right:-5%;bottom:-10px;height:22px;background:#74b82f;border-radius:50%}.id-brand{display:flex;align-items:center;gap:12px;position:relative;z-index:2}.id-brand img{width:58px;height:58px;object-fit:contain;border-radius:50%;background:#fff;padding:4px}.id-school-name{font-size:1.35rem;font-weight:800;line-height:1.05}.id-subtitle{font-size:.65rem;letter-spacing:1.4px;text-transform:uppercase;margin-top:5px}.id-main{padding:28px 22px 16px}.id-profile{display:flex;align-items:center;gap:17px;margin-bottom:16px}.photo-frame{width:105px;height:125px;flex:0 0 105px;border-radius:16px;padding:4px;background:linear-gradient(145deg,#0757a4,#74b82f)}.photo-frame img,.photo-placeholder{width:100%;height:100%;border-radius:12px;object-fit:cover;background:#fff}.photo-placeholder{display:flex;align-items:center;justify-content:center;text-align:center;font-size:.72rem;font-weight:800;color:#6b7d91}.student-name{font-size:1.35rem;font-weight:800;color:#082f67;margin:0 0 8px}.id-status{display:inline-block;background:#eaf7df;color:#388020;border-radius:999px;padding:5px 9px;font-size:.66rem;font-weight:800}.id-details{border-top:1px solid #e6edf5}.id-row{display:grid;grid-template-columns:115px 1fr;gap:8px;padding:8px 0;border-bottom:1px dashed #d7e0ea;font-size:.78rem}.id-label{font-weight:700;color:#617187}.id-value{font-weight:800;color:#152d4b}.id-lower{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:15px 22px 18px}.qr-box{width:100px;height:100px;border:2px solid #0b4d96;border-radius:10px;padding:5px;background:#fff}.qr-box img{width:100%;height:100%;display:block}.session-box{text-align:center;flex:1}.session-title{display:inline-block;background:#0757a4;color:#fff;border-radius:6px;padding:4px 8px;font-size:.63rem;font-weight:800}.session-year{font-size:1.35rem;color:#082f67;font-weight:900;margin:5px 0}.signature-line{border-top:1px solid #718096;padding-top:4px;font-size:.65rem;color:#526477}.id-footer{padding:10px 18px;background:linear-gradient(105deg,#082f67,#0757a4);color:#fff;text-align:center;font-size:.66rem;line-height:1.4;border-top:4px solid #74b82f}.no-print-actions{text-align:center;margin-top:24px}.qr-note{font-size:.7rem;color:#6c7a8b;margin-top:4px}@media(max-width:420px){.student-id-card{width:100%;max-width:360px}}@media print{html,body{background:#fff!important;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}body *{visibility:hidden!important}#printable-id-card,#printable-id-card *{visibility:visible!important}#printable-id-card{position:absolute;left:50%;top:10mm;transform:translateX(-50%);width:85.6mm!important;height:auto!important;margin:0!important;box-shadow:none!important;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}.no-print{display:none!important}@page{size:A4 portrait;margin:8mm}}
</style>

<section class="py-5 bg-light">
<div class="container">
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print"><div><h1 class="h3 fw-bold mb-1">Digital ID Card</h1><p class="text-secondary mb-0">C-Net Pathshala</p></div><a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">Back to Dashboard</a></div>
<div class="student-id-wrap">
<div id="printable-id-card" class="student-id-card">
<div class="id-topbar"><div class="id-brand"><img src="{{ asset('images/logo.png') }}" alt="C-Net Pathshala logo"><div><div class="id-school-name">C-Net Pathshala</div><div class="id-subtitle">Student Identity Card</div></div></div></div>
<div class="id-main">
<div class="id-profile"><div class="photo-frame">@if($student->photo)<img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->user->name }}">@else<div class="photo-placeholder">STUDENT<br>PHOTO</div>@endif</div><div><h2 class="student-name">{{ $student->user->name }}</h2><span class="id-status">ACTIVE STUDENT</span></div></div>
<div class="id-details">
<div class="id-row"><span class="id-label">Admission No.</span><span class="id-value">{{ $student->admission_no }}</span></div>
<div class="id-row"><span class="id-label">Class / Section</span><span class="id-value">{{ $student->class_name ?: '—' }} {{ $student->section ? '/ '.$student->section : '' }}</span></div>
<div class="id-row"><span class="id-label">Roll No.</span><span class="id-value">{{ $student->roll_no ?: '—' }}</span></div>
<div class="id-row"><span class="id-label">Date of Birth</span><span class="id-value">{{ $student->date_of_birth ? $student->date_of_birth->format('d-m-Y') : '—' }}</span></div>
<div class="id-row"><span class="id-label">Guardian</span><span class="id-value">{{ $student->guardian_name ?: '—' }}</span></div>
</div></div>
@php($verifyUrl = url('/student/verify/'.$student->admission_no))
<div class="id-lower"><div><div class="qr-box"><img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($verifyUrl) }}" alt="Student QR code"></div><div class="qr-note">Scan to verify</div></div><div class="session-box"><div class="session-title">ADMISSION SESSION</div><div class="session-year">2026-27</div><div class="signature-line">Authorised Signatory</div></div></div>
<div class="id-footer">A caring and activity-based learning environment for Play Group, Nursery, LKG and UKG.</div>
</div></div>
<div class="no-print no-print-actions"><button type="button" class="btn btn-primary px-4" onclick="window.print()">Print ID Card</button></div>
</div>
</section>
@endsection
