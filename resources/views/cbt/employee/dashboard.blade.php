@extends('layouts.app')

@section('title', 'Ujian Saya')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Ujian Saya</h3>
                <p class="text-subtitle text-muted">Halo, {{ $employee->name }}!</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ujian Saya</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    {{-- Pending Exams --}}
    @if($pendingExams->count() > 0)
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h4 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    Ujian yang Harus Dikerjakan ({{ $pendingExams->count() }})
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($pendingExams as $session)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 {{ $session->status === 'started' ? 'border-primary' : '' }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $session->exam->title }}</h5>
                                    <p class="text-muted mb-2">
                                        <span class="badge bg-info">{{ $session->exam->skill->name ?? '-' }}</span>
                                        <span class="badge bg-secondary">Level {{ $session->exam->target_level }}</span>
                                    </p>
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Durasi</small>
                                            <strong>{{ $session->exam->duration_minutes }} menit</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Soal</small>
                                            <strong>{{ $session->exam->questions->count() }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">KKM</small>
                                            <strong>{{ $session->exam->passing_score }}%</strong>
                                        </div>
                                    </div>

                                    @if($session->status === 'started')
                                        <div class="alert alert-warning py-2 mb-3">
                                            <i class="bi bi-clock"></i>
                                            <strong>Sedang Dikerjakan</strong>
                                            @if($session->getRemainingTime() > 0)
                                                <br><small>Sisa waktu: {{ floor($session->getRemainingTime() / 60) }} menit</small>
                                            @endif
                                        </div>
                                        <a href="{{ route('cbt.employee.take', $session) }}" class="btn btn-primary w-100">
                                            <i class="bi bi-play-circle"></i> Lanjutkan Ujian
                                        </a>
                                    @else
                                        <a href="{{ route('cbt.employee.show', $session) }}" class="btn btn-success w-100">
                                            <i class="bi bi-play-fill"></i> Mulai Ujian
                                        </a>
                                    @endif
                                </div>
                                <div class="card-footer text-muted">
                                    <small>Ditugaskan: {{ $session->created_at->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Completed --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Ujian Terakhir</h4>
            <a href="{{ route('cbt.employee.history') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body">
            @if($completedExams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ujian</th>
                                <th>Tanggal</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedExams as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->exam->title }}</strong>
                                        <br><small class="text-muted">{{ $session->exam->skill->name ?? '-' }}</small>
                                    </td>
                                    <td>{{ $session->finished_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>
                                        @if($session->score !== null)
                                            <span class="fw-bold {{ $session->score >= $session->exam->passing_score ? 'text-success' : 'text-danger' }}">
                                                {{ $session->score }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @switch($session->status)
                                            @case('submitted')
                                                <span class="badge bg-info">Menunggu Verifikasi</span>
                                                @break
                                            @case('verified_pass')
                                                <span class="badge bg-success">LULUS</span>
                                                @break
                                            @case('verified_fail')
                                                <span class="badge bg-danger">TIDAK LULUS</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('cbt.employee.result', $session) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">Belum ada ujian yang diselesaikan</p>
            @endif
        </div>
    </div>

    {{-- My Competencies Summary --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Kompetensi Saya</h4>
            <a href="{{ route('cbt.employee.competencies') }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
        </div>
        <div class="card-body">
            @if($competencies->count() > 0)
                <div class="row">
                    @foreach($competencies as $competency)
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>{{ $competency->skill->name ?? '-' }}</h6>
                                    <div class="display-6 fw-bold text-primary">
                                        Level {{ $competency->level }}
                                    </div>
                                    <small class="text-muted">
                                        @switch($competency->level)
                                            @case(1) Novice @break
                                            @case(2) Competent @break
                                            @case(3) Proficient @break
                                            @case(4) Expert @break
                                            @default Belum Dinilai
                                        @endswitch
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-3">Belum ada kompetensi tercatat</p>
            @endif
        </div>
    </div>
</section>
@endsection
