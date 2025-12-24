@extends('layouts.app-user')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Dashboard</h1>
            <p class="text-muted">Selamat datang kembali, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Pelatihan Aktif</p>
                            <h3 class="mb-0">5</h3>
                        </div>
                        <div class="text-primary" style="font-size: 2rem;">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Selesai</p>
                            <h3 class="mb-0">12</h3>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Dalam Proses</p>
                            <h3 class="mb-0">3</h3>
                        </div>
                        <div class="text-warning" style="font-size: 2rem;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Sertifikat</p>
                            <h3 class="mb-0">8</h3>
                        </div>
                        <div class="text-info" style="font-size: 2rem;">
                            <i class="fas fa-certificate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Training Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Pelatihan Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Safety Training - Level 1</h6>
                                    <p class="mb-0 text-muted small">Deadline: 31 Desember 2025</p>
                                </div>
                                <span class="badge bg-warning">Pending</span>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Quality Control Basics</h6>
                                    <p class="mb-0 text-muted small">Deadline: 15 Januari 2026</p>
                                </div>
                                <span class="badge bg-info">In Progress</span>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Machine Operation - Advanced</h6>
                                    <p class="mb-0 text-muted small">Deadline: 20 Januari 2026</p>
                                </div>
                                <span class="badge bg-success">Completed</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Info Profil</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    </div>
                    <dl class="row small">
                        <dt class="col-sm-5">Nama:</dt>
                        <dd class="col-sm-7">{{ Auth::user()->name }}</dd>
                        <dt class="col-sm-5">Email:</dt>
                        <dd class="col-sm-7">{{ Auth::user()->email }}</dd>
                        <dt class="col-sm-5">NIK:</dt>
                        <dd class="col-sm-7">-</dd>
                        <dt class="col-sm-5">Dept:</dt>
                        <dd class="col-sm-7">-</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
