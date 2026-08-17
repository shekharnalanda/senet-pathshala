@extends('layouts.app')

@section('title', 'Digital ID Card')

@section('content')
<style>
    .student-id-wrap {
        display: flex;
        justify-content: center;
    }

    .student-id-card {
        position: relative;
        width: 100%;
        max-width: 760px;
        min-height: 450px;
        overflow: hidden;
        border-radius: 24px;
        background: linear-gradient(135deg, #f7fbff 0%, #ffffff 56%, #edf8ee 100%);
        border: 1px solid rgba(16, 73, 134, .15);
        box-shadow: 0 24px 70px rgba(15, 57, 108, .18);
    }

    .student-id-card::before {
        content: '';
        position: absolute;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        right: -155px;
        top: -185px;
        background: linear-gradient(135deg, #0c4f98, #1397e8);
        opacity: .12;
    }

    .student-id-card::after {
        content: '';
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        left: -120px;
        bottom: -140px;
        background: linear-gradient(135deg, #4b9f2f, #86c43c);
        opacity: .10;
    }

    .id-topbar {
        position: relative;
        z-index: 2;
        padding: 22px 28px;
        background: linear-gradient(105deg, #082f67 0%, #0757a4 55%, #1397e8 100%);
        color: #fff;
    }

    .id-brand {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .id-brand img {
        width: 62px;
        height: 62px;
        object-fit: contain;
        border-radius: 50%;
        background: #fff;
        padding: 5px;
        box-shadow: 0 5px 18px rgba(0,0,0,.18);
    }

    .id-school-name {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: .2px;
    }

    .id-subtitle {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: .85;
        margin-top: 5px;
    }

    .id-session {
        font-weight: 700;
        font-size: .85rem;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.28);
        padding: 8px 13px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .id-main {
        position: relative;
        z-index: 2;
        padding: 28px;
    }

    .photo-frame {
        width: 155px;
        height: 185px;
        margin: 0 auto;
        border-radius: 18px;
        padding: 6px;
        background: linear-gradient(145deg, #0b63b6, #7ec242);
        box-shadow: 0 14px 28px rgba(17, 74, 136, .18);
    }

    .photo-frame img,
    .photo-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 13px;
        object-fit: cover;
        background: #fff;
    }

    .photo-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #6c7a8b;
        font-weight: 700;
        text-align: center;
        font-size: .85rem;
    }

    .student-name {
        margin: 0 0 15px;
        font-size: 1.75rem;
        font-weight: 800;
        color: #082f67;
    }

    .id-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 22px;
    }

    .id-field {
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(245, 249, 253, .85);
        border: 1px solid #e1edf7;
    }

    .id-field-label {
        display: block;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c7a8b;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .id-field-value {
        font-weight: 700;
        color: #172f49;
        font-size: .94rem;
    }

    .id-bottom {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 14px 28px 20px;
    }

    .id-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .78rem;
        font-weight: 800;
        color: #28752a;
        background: #edf8ed;
        border: 1px solid #cce9cc;
        border-radius: 999px;
        padding: 8px 12px;
    }

    .id-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #47a447;
    }

    .id-code {
        font-size: .78rem;
        color: #60758b;
        font-weight: 700;
        text-align: right;
    }

    @media (max-width: 576px) {
        .id-topbar,
        .id-main,
        .id-bottom { padding-left: 18px; padding-right: 18px; }
        .id-grid { grid-template-columns: 1fr; }
        .id-school-name { font-size: 1.25rem; }
        .id-session { display: none; }
    }

    @media print {
        html, body {
            background: #fff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body * { visibility: hidden !important; }

        #printable-id-card,
        #printable-id-card * { visibility: visible !important; }

        #printable-id-card {
            position: absolute;
            left: 50%;
            top: 18mm;
            transform: translateX(-50%);
            width: 180mm !important;
            max-width: 180mm !important;
            min-height: 105mm !important;
            margin: 0 !important;
            box-shadow: none !important;
            border-radius: 8mm !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print { display: none !important; }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    }
</style>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">
            <div>
                <h1 class="h3 fw-bold mb-1">Digital ID Card</h1>
                <p class="text-secondary mb-0">C-Net Pathshala</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">Back to Dashboard</a>
        </div>

        <div class="student-id-wrap">
            <div id="printable-id-card" class="student-id-card">
                <div class="id-topbar d-flex justify-content-between align-items-center gap-3">
                    <div class="id-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="C-Net Pathshala logo">
                        <div>
                            <div class="id-school-name">C-Net Pathshala</div>
                            <div class="id-subtitle">Student Identity Card</div>
                        </div>
                    </div>
                    <div class="id-session">Session 2026-27</div>
                </div>

                <div class="id-main">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-4 text-center">
                            <div class="photo-frame">
                                @if($student->photo)
                                    <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->user->name }}">
                                @else
                                    <div class="photo-placeholder">
                                        <div>STUDENT</div>
                                        <div>PHOTO</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8">
                            <h2 class="student-name">{{ $student->user->name }}</h2>
                            <div class="id-grid">
                                <div class="id-field">
                                    <span class="id-field-label">Admission No.</span>
                                    <span class="id-field-value">{{ $student->admission_no }}</span>
                                </div>
                                <div class="id-field">
                                    <span class="id-field-label">Class / Section</span>
                                    <span class="id-field-value">{{ $student->class_name ?: '—' }} {{ $student->section ? '/ '.$student->section : '' }}</span>
                                </div>
                                <div class="id-field">
                                    <span class="id-field-label">Roll No.</span>
                                    <span class="id-field-value">{{ $student->roll_no ?: '—' }}</span>
                                </div>
                                <div class="id-field">
                                    <span class="id-field-label">Date of Birth</span>
                                    <span class="id-field-value">{{ $student->date_of_birth ? $student->date_of_birth->format('d-m-Y') : '—' }}</span>
                                </div>
                                <div class="id-field">
                                    <span class="id-field-label">Guardian</span>
                                    <span class="id-field-value">{{ $student->guardian_name ?: '—' }}</span>
                                </div>
                                <div class="id-field">
                                    <span class="id-field-label">Guardian Phone</span>
                                    <span class="id-field-value">{{ $student->guardian_phone ?: '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="id-bottom">
                    <div class="id-status"><span class="id-status-dot"></span> ACTIVE STUDENT</div>
                    <div class="id-code">ID: {{ $student->admission_no }}<br>C-Net Pathshala</div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 no-print">
            <button type="button" class="btn btn-primary px-4" onclick="window.print()">Print ID Card</button>
        </div>
    </div>
</section>
@endsection
