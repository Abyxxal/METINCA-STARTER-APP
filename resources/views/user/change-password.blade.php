@extends('layouts.app-user')

@section('title', 'Ganti Password')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Ganti Password</h3>
                <p class="text-subtitle text-muted">Keamanan akun kamu dulu ya sebelum mulai</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Ganti Password</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-shield-lock me-2"></i>
                        Akun kamu masih memakai password default dari admin.
                        Buat password baru yang hanya kamu tahu untuk melanjutkan.
                    </div>

                    <form action="{{ route('user.password.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required
                                   minlength="8"
                                   autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <small class="text-muted">Minimal 8 karakter.</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Ulangi Password Baru</label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required
                                   autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-shield-check me-1"></i> Simpan Password Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
