<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'C-Net Pathshala')</title>
    <meta name="description" content="C-Net Pathshala - Quality early childhood education.">
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="top-bar">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <span><i class="fa-solid fa-phone me-1"></i>7004773247</span>
        <span><i class="fa-solid fa-envelope me-1"></i>cnetbiharsharif@gmail.com</span>
        <span>Admission Open 2026-27</span>
    </div>
</div>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" width="54" height="54" alt="C-Net Pathshala logo">
            <span class="fw-bold">C-Net Pathshala</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/admission') }}">Admission</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/gallery') }}">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
                <li class="nav-item ms-lg-3"><a class="btn btn-primary rounded-pill px-3" href="{{ route('student.login') }}">Student Login</a></li>
                <li class="nav-item ms-lg-2"><a class="btn btn-outline-secondary rounded-pill px-3" href="{{ route('admin.login') }}">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>
<main>