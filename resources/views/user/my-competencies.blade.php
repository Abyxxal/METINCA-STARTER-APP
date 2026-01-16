@extends('layouts.app-user')

@section('title', 'Kompetensi Saya')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kompetensi Saya</h3>
                <p class="text-subtitle text-muted">Daftar kompetensi dan skill yang telah Anda kuasai</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kompetensi Saya</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            @forelse($competencies as $skillName => $competency)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ $skillName }}</h5>
                                <span class="badge bg-light-info">{{ $competency['skill']->code }}</span>
                            </div>
                            <div class="text-end">
                                <div class="badge 
                                    @if($competency['current_level'] == 1) bg-light-success
                                    @elseif($competency['current_level'] == 2) bg-light-warning
                                    @elseif($competency['current_level'] == 3) bg-light-danger
                                    @else bg-light-dark
                                    @endif font-bold">
                                    Level {{ $competency['current_level'] }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar 
                                    @if($competency['current_level'] == 1) bg-success
                                    @elseif($competency['current_level'] == 2) bg-warning
                                    @elseif($competency['current_level'] == 3) bg-danger
                                    @else bg-dark
                                    @endif" 
                                    style="width: {{ ($competency['current_level'] / 4) * 100 }}%;">
                                </div>
                            </div>
                            <small class="text-muted">Level {{ $competency['current_level'] }} dari 4</small>
                        </div>

                        <div class="text-center mb-3">
                            <div class="mb-1">
                                <i class="bi bi-calendar-check text-primary fs-4"></i>
                            </div>
                            <h6 class="mb-0">{{ $competency['latest_date']?->format('d M Y') }}</h6>
                            <small class="text-muted">Terakhir Diperbarui</small>
                        </div>

                        <button class="btn btn-outline-primary btn-sm w-100" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $competency['skill']->id }}">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </button>

                        <div class="collapse mt-3" id="collapse{{ $competency['skill']->id }}">
                            <div class="card card-body bg-light">
                                <h6 class="mb-2">Riwayat Ujian:</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($competency['sessions'] as $session)
                                    <li class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="d-block fw-bold">{{ $session->exam->title }}</small>
                                                <small class="text-muted">
                                                    Level {{ $session->exam->target_level }} - 
                                                    Nilai: {{ $session->score }}
                                                </small>
                                            </div>
                                            <a href="{{ route('cbt.employee.result', $session->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-award fs-1 text-muted d-block mb-3"></i>
                        <h5 class="text-muted">Belum Ada Kompetensi</h5>
                        <p class="text-muted">Anda belum memiliki kompetensi yang tersertifikasi. Selesaikan pelatihan untuk mendapatkan kompetensi.</p>
                        <a href="{{ route('user.my-training') }}" class="btn btn-primary">
                            <i class="bi bi-book"></i> Lihat Pelatihan Saya
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        @if($competencies->count() > 0)
        <!-- Summary Card -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ringkasan Kompetensi</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldProfile"></i>
                                </div>
                                <h6 class="text-muted font-semibold">Total Skill</h6>
                                <h6 class="font-extrabold mb-0">{{ $competencies->count() }}</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldStar"></i>
                                </div>
                                <h6 class="text-muted font-semibold">Level Tertinggi</h6>
                                <h6 class="font-extrabold mb-0">{{ $competencies->max('current_level') }}</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-icon red mb-2">
                                    <i class="iconly-boldActivity"></i>
                                </div>
                                <h6 class="text-muted font-semibold">Terakhir Update</h6>
                                <h6 class="font-extrabold mb-0">{{ $competencies->max('latest_date')?->format('M Y') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>
</div>
@endsection
