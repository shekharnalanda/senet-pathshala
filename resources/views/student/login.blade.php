<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - C-Net Pathshala</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="C-Net Pathshala logo" width="80" class="mb-3">
                        <h3 class="fw-bold mb-1">Student Login</h3>
                        <p class="text-secondary mb-0">C-Net Pathshala</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('student.login.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Sign In</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('home') }}" class="text-decoration-none">Back to website</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
