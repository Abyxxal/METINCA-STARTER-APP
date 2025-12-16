<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metinca - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.3/dist/sweetalert2.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h1 class="brand-name">Daftar Akun Baru</h1>
                <p class="brand-tagline">Buat akun untuk mengakses Metinca Starter App</p>
            </div>

            <!-- Alert Section -->
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Register Form -->
            <form action="{{ route('register.store') }}" method="POST" id="registerForm">
                @csrf
                
                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="bi bi-person-fill me-1"></i>Nama Lengkap
                    </label>
                    <div class="input-group">
                        <input type="text" name="name" class="form-control with-icon" id="name" 
                               placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                        <span class="input-icon">
                            <i class="bi bi-person"></i>
                        </span>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope-fill me-1"></i>Email
                    </label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control with-icon" id="email" 
                               placeholder="Masukkan email" value="{{ old('email') }}" required>
                        <span class="input-icon">
                            <i class="bi bi-envelope"></i>
                        </span>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock-fill me-1"></i>Password
                    </label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control with-icon" id="password" 
                               placeholder="Masukkan password" required>
                        <span class="input-icon password-toggle" onclick="togglePassword('password')">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                    <small class="text-muted">Minimal 8 karakter</small>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="bi bi-lock-fill me-1"></i>Konfirmasi Password
                    </label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" class="form-control with-icon" 
                               id="password_confirmation" placeholder="Konfirmasi password" required>
                        <span class="input-icon password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="bi bi-eye" id="toggleIcon2"></i>
                        </span>
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" class="btn-login">
                    <i class="bi bi-person-plus me-2"></i>Daftar
                </button>
            </form>

            <!-- Login Link -->
            <div class="signup-link">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a>
            </div>
            
            <div class="signup-link">
                Kembali ke <a href="/home">Homepage</a>
            </div>
        </div>

        <!-- Background Decoration -->
        <div class="bg-decoration"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.3/dist/sweetalert2.all.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = fieldId === 'password' ? document.getElementById('toggleIcon') : document.getElementById('toggleIcon2');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
