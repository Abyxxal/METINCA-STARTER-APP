@extends('layouts.app')

@section('title', 'Detail Sesi Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Sesi Ujian</h3>
                <p class="text-subtitle text-muted">{{ $session->exam->title }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.sessions.index') }}">Sesi Ujian</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="row">
        <div class="col-md-8">
            {{-- Session Info --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Sesi</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">Karyawan</td>
                                    <td>
                                        <strong>{{ $session->employee->name ?? $session->employee_nik }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIK</td>
                                    <td>{{ $session->employee_nik }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Divisi</td>
                                    <td>{{ $session->employee->division->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Posisi</td>
                                    <td>{{ $session->employee->position->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">Ujian</td>
                                    <td>
                                        <a href="{{ route('cbt.admin.exams.show', $session->exam) }}">{{ $session->exam->title }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Skill</td>
                                    <td><span class="badge bg-info">{{ $session->exam->skill->name ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Target Level</td>
                                    <td><span class="badge bg-secondary">Level {{ $session->exam->target_level }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">KKM</td>
                                    <td>{{ $session->exam->passing_score }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Answers Review --}}
            @if($session->status !== 'assigned')
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Jawaban ({{ $session->answers->count() }} / {{ $session->exam->questions->count() }})</h4>
                    </div>
                    <div class="card-body">
                        @forelse($session->exam->questions as $i => $question)
                            @php
                                $answer = $session->answers->where('question_id', $question->id)->first();
                            @endphp
                            <div class="mb-4 p-3 border rounded {{ $answer?->is_correct ? 'border-success bg-light' : ($answer ? 'border-danger' : 'border-secondary') }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>{{ $i + 1 }}. {{ $question->question_text }}</strong>
                                    @if($question->type !== 'essay')
                                        <span class="badge bg-{{ $answer?->is_correct ? 'success' : ($answer ? 'danger' : 'secondary') }}">
                                            {{ $answer?->is_correct ? '✓ Benar' : '✗ Salah' }}
                                        </span>
                                    @endif
                                </div>

                                @if($question->type !== 'essay')
                                    <div class="ms-3">
                                        @foreach($question->options as $key => $option)
                                            <div class="mb-1 {{ $answer?->selected_answer === $key ? 'fw-bold' : '' }} {{ $question->correct_answer === $key ? 'text-success' : '' }}">
                                                @if($answer?->selected_answer === $key)
                                                    <i class="bi bi-{{ $answer->is_correct ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }}"></i>
                                                @elseif($question->correct_answer === $key)
                                                    <i class="bi bi-check-circle text-success"></i>
                                                @else
                                                    <i class="bi bi-circle"></i>
                                                @endif
                                                {{ $key }}. {{ $option }}
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Essay Answer --}}
                                    <div class="ms-3">
                                        <div class="card bg-light-info">
                                            <div class="card-body">
                                                <h6 class="mb-2">
                                                    <i class="bi bi-file-earmark-text"></i> Jawaban Karyawan:
                                                </h6>
                                                <div class="bg-white p-3 rounded border" style="min-height: 100px; white-space: pre-wrap;">
                                                    {{ $answer?->selected_answer ?? 'Tidak dijawab' }}
                                                </div>
                                                
                                                @if($session->status === 'submitted')
                                                    {{-- Manual Grading for Essay --}}
                                                    <hr>
                                                    <div class="mt-3">
                                                        <label class="form-label fw-bold">
                                                            <i class="bi bi-star"></i> Penilaian Essay:
                                                        </label>
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" 
                                                                        name="essay_grade_{{ $question->id }}" 
                                                                        id="correct_{{ $question->id }}"
                                                                        value="1"
                                                                        {{ $answer?->is_correct ? 'checked' : '' }}>
                                                                    <label class="form-check-label text-success fw-bold" for="correct_{{ $question->id }}">
                                                                        <i class="bi bi-check-circle-fill"></i> Benar
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" 
                                                                        name="essay_grade_{{ $question->id }}" 
                                                                        id="incorrect_{{ $question->id }}"
                                                                        value="0"
                                                                        {{ $answer && !$answer->is_correct ? 'checked' : '' }}>
                                                                    <label class="form-check-label text-danger fw-bold" for="incorrect_{{ $question->id }}">
                                                                        <i class="bi bi-x-circle-fill"></i> Salah
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted d-block mt-2">
                                                            <i class="bi bi-info-circle"></i> Pilih penilaian untuk essay ini. Nilai akan tersimpan saat Anda klik tombol verifikasi.
                                                        </small>
                                                    </div>
                                                @else
                                                    {{-- Display grading result after verification --}}
                                                    @if($answer)
                                                        <div class="alert alert-{{ $answer->is_correct ? 'success' : 'danger' }} mt-3 mb-0">
                                                            <i class="bi bi-{{ $answer->is_correct ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                                                            <strong>Penilaian: {{ $answer->is_correct ? 'Benar' : 'Salah' }}</strong>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted">Tidak ada soal</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            {{-- Status & Score --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Status & Nilai</h4>
                </div>
                <div class="card-body text-center">
                    @switch($session->status)
                        @case('assigned')
                            <span class="badge bg-secondary fs-5 mb-3">Ditugaskan</span>
                            @break
                        @case('started')
                            <span class="badge bg-warning text-dark fs-5 mb-3">Sedang Dikerjakan</span>
                            @break
                        @case('submitted')
                            <span class="badge bg-info fs-5 mb-3">Menunggu Verifikasi</span>
                            @break
                        @case('verified_pass')
                            <span class="badge bg-primary fs-5 mb-3">Lulus - Menunggu Approval</span>
                            @break
                        @case('verified_fail')
                            <span class="badge bg-danger fs-5 mb-3">TIDAK LULUS</span>
                            @break
                        @case('approved')
                            <span class="badge bg-success fs-5 mb-3">Disetujui Manager</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-dark fs-5 mb-3">Ditolak Manager</span>
                            @break
                    @endswitch

                    @if($session->score !== null)
                        <div class="display-4 fw-bold {{ $session->score >= $session->exam->passing_score ? 'text-success' : 'text-danger' }}">
                            {{ $session->score }}
                        </div>
                        <p class="text-muted">dari 100 (KKM: {{ $session->exam->passing_score }})</p>
                    @endif

                    <hr>

                    <table class="table table-sm table-borderless text-start">
                        <tr>
                            <td class="text-muted">Ditugaskan</td>
                            <td>{{ $session->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @if($session->scheduled_start_at)
                            <tr>
                                <td class="text-muted">Jadwal Mulai</td>
                                <td>{{ $session->getFormattedScheduledStart() }} WIB</td>
                            </tr>
                        @endif
                        @if($session->deadline_at)
                            <tr>
                                <td class="text-muted">Batas Akhir</td>
                                <td>{{ $session->getFormattedDeadline() }} WIB</td>
                            </tr>
                        @endif
                        @if($session->started_at)
                            <tr>
                                <td class="text-muted">Mulai</td>
                                <td>{{ $session->started_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endif
                        @if($session->finished_at)
                            <tr>
                                <td class="text-muted">Selesai</td>
                                <td>{{ $session->finished_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endif
                        @if($session->verifier)
                            <tr>
                                <td class="text-muted">Diverifikasi</td>
                                <td>
                                    {{ $session->verifier->name }}
                                    <br><small>{{ $session->verified_at?->format('d M Y H:i') }}</small>
                                </td>
                            </tr>
                        @endif
                        @if($session->manager)
                            <tr>
                                <td class="text-muted">Keputusan Manager</td>
                                <td>
                                    @if($session->manager_decision === 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($session->manager_decision === 'rejected')
                                        <span class="badge bg-dark">Ditolak</span>
                                    @endif
                                    <br><small>{{ $session->manager->name }} &bull; {{ $session->decided_at?->format('d M Y H:i') }}</small>
                                    @if($session->manager_notes)
                                        <br><small class="text-muted fst-italic">"{{ $session->manager_notes }}"</small>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Actions --}}
            @if($session->status === 'submitted')
                <div class="card">
                    <div class="card-header bg-light-warning">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-clipboard-check"></i> Penilaian Hasil Ujian
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>Hasil Penilaian Otomatis:</strong><br>
                            Nilai: <strong class="fs-5">{{ $session->score }}%</strong> | 
                            KKM: <strong>{{ $session->exam->passing_score }}%</strong> | 
                            Status: <strong class="{{ $session->isPassed() ? 'text-success' : 'text-danger' }}">
                                {{ $session->isPassed() ? '✓ LULUS' : '✗ TIDAK LULUS' }}
                            </strong>
                        </div>

                        <form action="{{ route('cbt.admin.sessions.verify', $session) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-chat-left-text"></i> Catatan untuk Karyawan 
                                    <small class="text-muted">(Opsional)</small>
                                </label>
                                <textarea name="notes" class="form-control" rows="4" 
                                    placeholder="Tuliskan catatan, saran, atau feedback untuk karyawan. Misalnya: area yang perlu diperbaiki, hal yang sudah bagus, atau rekomendasi untuk pelatihan berikutnya."></textarea>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> Catatan ini akan terlihat oleh karyawan setelah verifikasi.
                                </small>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Verifikasi & Simpan Hasil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Manager Approval --}}
            @if($session->status === 'verified_pass' && $session->isPendingManagerApproval() && Auth::user()->isManager())
                <div class="card">
                    <div class="card-header bg-light-primary">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-shield-check"></i> Persetujuan Kenaikan Level
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>{{ $session->employee->name }}</strong> lulus ujian dengan nilai <strong>{{ $session->score }}%</strong>.<br>
                            Skill <strong>{{ $session->exam->skill->name ?? '-' }}</strong> akan dinaikkan ke <strong>Level {{ $session->exam->target_level }}</strong>.
                        </div>

                        {{-- Approve Form --}}
                        <form action="{{ route('cbt.admin.sessions.approve-level', $session) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="manager_notes" class="form-control" rows="2" placeholder="Catatan persetujuan..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-check-circle"></i> Setujui Kenaikan Level
                            </button>
                        </form>

                        {{-- Reject Form --}}
                        <form action="{{ route('cbt.admin.sessions.reject-level', $session) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="manager_notes" class="form-control" rows="2" placeholder="Alasan penolakan..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Tolak Kenaikan Level
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <a href="{{ route('cbt.admin.sessions.index') }}" class="btn btn-secondary btn-block w-100">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
