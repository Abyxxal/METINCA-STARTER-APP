@extends('layouts.app')

@section('title', $exam->title)

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $exam->title }}</h3>
                <p class="text-subtitle text-muted">Informasi Ujian</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('cbt.employee.dashboard') }}">CBT</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.employee.competencies') }}">Kompetensi</a></li>
                        <li class="breadcrumb-item active">{{ $exam->title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text"></i> {{ $exam->title }}
                    </h4>
                </div>
                <div class="card-body">
                    @php
                        $competency = $employee->competencies()->where('skill_id', $exam->skill_id)->first();
                        $currentLevel = $competency ? $competency->level : 0;
                        $canTake = ($exam->target_level <= $currentLevel + 1);
                    @endphp

                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <div class="p-3 border rounded">
                                <i class="bi bi-award fs-3 text-primary"></i>
                                <p class="mb-0 fw-bold">{{ $exam->skill->name ?? '-' }}</p>
                                <small class="text-muted">Skill</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded">
                                <span class="fs-1 fw-bold text-success">{{ $exam->target_level }}</span>
                                <p class="mb-0">Target Level</p>
                                <small class="text-muted">Level saat ini: {{ $currentLevel }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded">
                                <span class="fs-3 fw-bold">{{ $exam->questions->count() }}</span>
                                <p class="mb-0">Jumlah Soal</p>
                                <small class="text-muted">Soal</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded">
                                <span class="fs-3 fw-bold">{{ $exam->duration_minutes }}</span>
                                <p class="mb-0">Durasi</p>
                                <small class="text-muted">Menit</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h5><i class="bi bi-info-circle"></i> Informasi Ujian</h5>
                        <ul class="mb-0">
                            <li>Nilai KKM (Kriteria Ketuntasan Minimal): <strong>{{ $exam->passing_score }}%</strong></li>
                            <li>Setelah memulai ujian, waktu akan berjalan otomatis.</li>
                            <li>Jawaban akan tersimpan otomatis saat Anda memilih pilihan.</li>
                            <li>Hasil ujian akan diperiksa dan diverifikasi oleh Admin.</li>
                        </ul>
                    </div>

                    @if($exam->description)
                        <div class="mb-4">
                            <h5>Deskripsi</h5>
                            <p>{{ $exam->description }}</p>
                        </div>
                    @endif

                    @if(!$canTake)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Anda belum memenuhi syarat</strong>
                            <p class="mb-0">Untuk mengikuti ujian Level {{ $exam->target_level }}, Anda harus memiliki Level {{ $exam->target_level - 1 }} terlebih dahulu.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cbt.employee.competencies') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        @if($canTake)
                            <form action="{{ route('cbt.employee.register', $exam) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-person-plus"></i> Daftar Ujian Ini
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-lock"></i> Belum Memenuhi Syarat
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
