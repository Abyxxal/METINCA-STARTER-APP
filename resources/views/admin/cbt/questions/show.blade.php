@extends('layouts.app')

@section('title', 'Detail Soal')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Soal</h3>
                <p class="text-subtitle text-muted">Preview dan informasi soal</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.questions.index') }}">Bank Soal</a></li>
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Soal</h4>
                    <div>
                        <a href="{{ route('cbt.admin.questions.edit', $question) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <p class="fs-5">{{ $question->question_text }}</p>
                    </div>

                    @if($question->type !== 'essay')
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Pilihan Jawaban:</h6>
                            @foreach($question->options as $key => $option)
                                <div class="d-flex align-items-center mb-2 p-2 rounded {{ $question->correct_answer === $key ? 'bg-success text-white' : 'bg-light' }}">
                                    <span class="badge {{ $question->correct_answer === $key ? 'bg-white text-success' : 'bg-secondary' }} me-3">{{ $key }}</span>
                                    <span>{{ $option }}</span>
                                    @if($question->correct_answer === $key)
                                        <i class="bi bi-check-circle-fill ms-auto"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-check-circle"></i>
                            <strong>Jawaban Benar:</strong> {{ $question->correct_answer }}
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i>
                            Soal essay - jawaban dinilai manual oleh admin.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Informasi</h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Skill</td>
                            <td><span class="badge bg-info">{{ $question->skill->name ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Level</td>
                            <td><span class="badge bg-secondary">Level {{ $question->for_level }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td>
                                @switch($question->type)
                                    @case('multiple_choice') Pilihan Ganda @break
                                    @case('true_false') Benar/Salah @break
                                    @case('essay') Essay @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @if($question->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat</td>
                            <td>{{ $question->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diupdate</td>
                            <td>{{ $question->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($question->exams->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Digunakan di Ujian</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($question->exams as $exam)
                            <li class="list-group-item">
                                <a href="{{ route('cbt.admin.exams.show', $exam) }}">{{ $exam->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('cbt.admin.questions.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</section>
@endsection
