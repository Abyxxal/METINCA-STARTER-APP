@extends('layouts.app')

@section('title', 'Setup Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Setup Ujian</h3>
                <p class="text-subtitle text-muted">Kelola ujian kompetensi karyawan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Setup Ujian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Daftar Ujian</h4>
            <a href="{{ route('cbt.admin.exams.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Buat Ujian Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Nama Ujian</th>
                            <th width="15%">Skill</th>
                            <th width="10%">Target Level</th>
                            <th width="10%">KKM</th>
                            <th width="10%">Durasi</th>
                            <th width="10%">Soal</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $i => $exam)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <a href="{{ route('cbt.admin.exams.show', $exam) }}" class="fw-bold text-primary">
                                        {{ $exam->title }}
                                    </a>
                                    @if($exam->description)
                                        <br><small class="text-muted text-truncate" style="max-width: 200px;">{{ Str::limit($exam->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $exam->skill->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Level {{ $exam->target_level }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $exam->passing_score }}</span>
                                </td>
                                <td>{{ $exam->duration_minutes }} menit</td>
                                <td>
                                    <span class="badge bg-primary">{{ $exam->exam_questions_count }} soal</span>
                                </td>
                                <td>
                                    @if($exam->is_published)
                                        <span class="badge bg-success">Dipublikasi</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('cbt.admin.exams.show', $exam) }}" class="btn btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('cbt.admin.exams.edit', $exam) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('cbt.admin.exams.toggle-publish', $exam) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-{{ $exam->is_published ? 'secondary' : 'success' }}" title="{{ $exam->is_published ? 'Unpublish' : 'Publish' }}">
                                                <i class="bi bi-{{ $exam->is_published ? 'eye-slash' : 'globe' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada ujian. <a href="{{ route('cbt.admin.exams.create') }}">Buat ujian pertama</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
