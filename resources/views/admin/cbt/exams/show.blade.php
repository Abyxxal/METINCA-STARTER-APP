@extends('layouts.app')

@section('title', 'Detail Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $exam->title }}</h3>
                <p class="text-subtitle text-muted">Detail dan konfigurasi ujian</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.exams.index') }}">Setup Ujian</a></li>
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
            {{-- Exam Info --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Informasi Ujian</h4>
                    <div>
                        <a href="{{ route('cbt.admin.exams.edit', $exam) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('cbt.admin.exams.toggle-publish', $exam) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-{{ $exam->is_published ? 'secondary' : 'success' }}">
                                <i class="bi bi-{{ $exam->is_published ? 'eye-slash' : 'globe' }}"></i>
                                {{ $exam->is_published ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if($exam->description)
                        <p class="text-muted">{{ $exam->description }}</p>
                    @endif

                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-primary mb-0">{{ $exam->passing_score }}</h2>
                                <small class="text-muted">KKM (Passing Score)</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-info mb-0">{{ $exam->duration_minutes }}</h2>
                                <small class="text-muted">Menit</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-success mb-0">{{ $exam->examQuestions->count() }}</h2>
                                <small class="text-muted">Soal</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-warning mb-0">{{ $exam->examQuestions->sum('weight') }}</h2>
                                <small class="text-muted">Total Bobot</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Questions --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Soal ({{ $exam->examQuestions->count() }})</h4>
                </div>
                <div class="card-body">
                    @if($exam->examQuestions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="60%">Soal</th>
                                        <th width="15%">Level</th>
                                        <th width="10%">Bobot</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->examQuestions as $i => $question)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 400px;">
                                                    {{ $question->question_text }}
                                                </div>
                                            </td>
                                            <td><span class="badge bg-secondary">Level {{ $question->for_level }}</span></td>
                                            <td><span class="badge bg-primary">{{ $question->weight }}</span></td>
                                            <td>
                                                @if($question->question)
                                                    <a href="{{ route('cbt.admin.questions.show', $question->question) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Belum ada soal untuk ujian ini. <a href="{{ route('cbt.admin.exams.edit', $exam) }}">Tambah soal sekarang</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- Exam Meta --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Info</h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($exam->is_published)
                                    <span class="badge bg-success">Dipublikasi</span>
                                @else
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Skill</td>
                            <td><span class="badge bg-info">{{ $exam->skill->name ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Target Level</td>
                            <td><span class="badge bg-secondary">Level {{ $exam->target_level }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>{{ $exam->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Recent Sessions --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Sesi Terbaru</h4>
                    <a href="{{ route('cbt.admin.sessions.index', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($exam->sessions->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($exam->sessions->take(5) as $session)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $session->employee->name ?? $session->employee_nik }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $session->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div>
                                        @switch($session->status)
                                            @case('assigned')
                                                <span class="badge bg-secondary">Ditugaskan</span>
                                                @break
                                            @case('started')
                                                <span class="badge bg-warning text-dark">Sedang Dikerjakan</span>
                                                @break
                                            @case('submitted')
                                                <span class="badge bg-info">Menunggu Verifikasi</span>
                                                @break
                                            @case('verified_pass')
                                                <span class="badge bg-success">Lulus ({{ $session->formatted_score }})</span>
                                                @break
                                            @case('verified_fail')
                                                <span class="badge bg-danger">Tidak Lulus ({{ $session->formatted_score }})</span>
                                                @break
                                        @endswitch
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center py-3">Belum ada sesi ujian</p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('cbt.admin.sessions.create', ['exam_id' => $exam->id]) }}" class="btn btn-success btn-block w-100 mb-2">
                        <i class="bi bi-person-plus"></i> Tugaskan ke Karyawan
                    </a>
                    <a href="{{ route('cbt.admin.exams.index') }}" class="btn btn-secondary btn-block w-100">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
