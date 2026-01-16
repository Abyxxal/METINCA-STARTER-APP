@extends('layouts.app-user')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <h3>Dashboard</h3>
    <p class="text-subtitle text-muted">Selamat datang kembali, {{ Auth::user()->name }}!</p>
</div>

<div class="page-content">
    <section class="row">
        <!-- Stats Cards -->
        <div class="col-12">
            <div class="row">
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldBookmark"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Pelatihan Aktif</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['active'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Selesai</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['completed'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldActivity"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Dalam Proses</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['in_progress'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <i class="iconly-boldStar"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Sertifikat</h6>
                                    <h6 class="font-extrabold mb-0">{{ $stats['certificates'] }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="row">
            <!-- Recent Training Section -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pelatihan Terbaru</h4>
                    </div>
                    <div class="card-body">
                        @forelse($recentSessions as $session)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $session->exam->title }}</h6>
                                <p class="mb-0 text-muted small">
                                    <span class="badge bg-light-info">{{ $session->exam->skill->code }}</span>
                                    <span class="badge bg-light-secondary">Level {{ $session->exam->target_level }}</span>
                                    • Deadline: {{ $session->deadline?->format('d M Y') ?? '-' }}
                                </p>
                            </div>
                            <div>
                                @if($session->status === 'assigned')
                                    <span class="badge bg-light-warning">Pending</span>
                                @elseif($session->status === 'started')
                                    <span class="badge bg-light-info">In Progress</span>
                                @elseif($session->status === 'submitted')
                                    <span class="badge bg-light-secondary">Submitted</span>
                                @elseif($session->status === 'verified_pass')
                                    <span class="badge bg-light-success">Lulus</span>
                                @else
                                    <span class="badge bg-light-danger">Tidak Lulus</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p>Belum ada pelatihan</p>
                        </div>
                        @endforelse
                        
                        @if($recentSessions->count() > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('user.my-training') }}" class="btn btn-primary">
                                Lihat Semua Pelatihan
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Info Profil</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" 
                                     class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light-primary d-inline-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="bi bi-person fs-2 text-primary"></i>
                                </div>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted">Nama:</td>
                                        <td class="fw-bold">{{ Auth::user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Email:</td>
                                        <td>{{ Auth::user()->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">NIK:</td>
                                        <td>{{ Auth::user()->employee->nik ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Dept:</td>
                                        <td>{{ Auth::user()->employee->department->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Divisi:</td>
                                        <td>{{ Auth::user()->employee->division->name ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('user.my-profile') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-person"></i> Lihat Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
