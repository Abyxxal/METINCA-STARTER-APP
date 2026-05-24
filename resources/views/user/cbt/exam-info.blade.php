@extends('layouts.app')

@section('title', $session->exam->title)

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $session->exam->title }}</h3>
                <p class="text-subtitle text-muted">Informasi ujian sebelum memulai</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('cbt.employee.dashboard') }}">Ujian Saya</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Info Ujian</li>
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
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-clipboard-check display-1 text-primary"></i>
                    </div>
                    
                    <h2 class="mb-3">{{ $session->exam->title }}</h2>
                    
                    <p class="text-muted mb-4">
                        <span class="badge bg-info fs-6">{{ $session->exam->skill->name ?? '-' }}</span>
                        <span class="badge bg-secondary fs-6">Target: Level {{ $session->exam->target_level }}</span>
                    </p>

                    @if($session->exam->description)
                        <p class="mb-4">{{ $session->exam->description }}</p>
                    @endif

                    <div class="row justify-content-center mb-4">
                        <div class="col-md-3">
                            <div class="bg-light rounded p-3">
                                <h3 class="text-primary mb-0">{{ $session->exam->questions->count() }}</h3>
                                <small class="text-muted">Soal</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-light rounded p-3">
                                <h3 class="text-info mb-0">{{ $session->exam->duration_minutes }}</h3>
                                <small class="text-muted">Menit</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-light rounded p-3">
                                <h3 class="text-warning mb-0">{{ $session->exam->passing_score }}%</h3>
                                <small class="text-muted">Nilai Lulus (KKM)</small>
                            </div>
                        </div>
                    </div>

                    @if($session->scheduled_start_at || $session->deadline_at)
                    <div class="row justify-content-center mb-4">
                        @if($session->scheduled_start_at)
                        <div class="col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block"><i class="bi bi-calendar-event"></i> Jadwal Mulai</small>
                                <strong>{{ $session->getFormattedScheduledStart() }} WIB</strong>
                            </div>
                        </div>
                        @endif
                        @if($session->deadline_at)
                        <div class="col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block"><i class="bi bi-calendar-x"></i> Batas Akhir</small>
                                <strong>{{ $session->getFormattedDeadline() }} WIB</strong>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="alert alert-warning text-start">
                        <h5><i class="bi bi-exclamation-triangle"></i> Perhatian!</h5>
                        <ul class="mb-0">
                            <li>Pastikan koneksi internet Anda stabil</li>
                            <li>Waktu akan berjalan setelah Anda menekan tombol "Mulai Ujian"</li>
                            <li>Jawaban akan otomatis tersimpan setiap kali Anda memilih jawaban</li>
                            <li>Ujian akan otomatis berakhir jika waktu habis</li>
                            <li>Anda tidak dapat kembali ke soal sebelumnya setelah mengakhiri ujian</li>
                        </ul>
                    </div>

                    <form action="{{ route('cbt.employee.start', $session->id) }}" method="POST">
                        @csrf
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                            <label class="form-check-label" for="agreeTerms">
                                Saya sudah membaca dan memahami instruksi di atas
                            </label>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('user.my-training') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-play-fill"></i> Mulai Ujian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
