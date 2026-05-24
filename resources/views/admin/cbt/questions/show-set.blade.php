@extends('layouts.app')

@section('title', 'Detail Set Soal')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $setInfo->set_title }}</h3>
                <p class="text-subtitle text-muted">ID: {{ $setInfo->question_set_id }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.questions.index') }}">Bank Soal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Set</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    {{-- Info Set --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Skill:</strong><br>
                    <span class="badge bg-info">{{ $setInfo->skill->name }}</span>
                </div>
                <div class="col-md-2">
                    <strong>Level:</strong><br>
                    <span class="badge bg-secondary">Level {{ $setInfo->for_level }}</span>
                </div>
                <div class="col-md-2">
                    <strong>Jumlah Soal:</strong><br>
                    <span class="badge bg-primary">{{ $questions->count() }} soal</span>
                </div>
                <div class="col-md-2">
                    <strong>Status:</strong><br>
                    @if($setInfo->status === 'active')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('cbt.admin.questions.edit-set', $setInfo->question_set_id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Set
                    </a>
                    <form action="{{ route('cbt.admin.questions.destroy-set', $setInfo->question_set_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus seluruh set soal ini ({{ $questions->count() }} soal)?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Hapus Set
                        </button>
                    </form>
                    <a href="{{ route('cbt.admin.questions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Soal --}}
    @foreach($questions as $index => $question)
    <div class="card mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Soal #{{ $index + 1 }}</h5>
            <div>
                @switch($question->type)
                    @case('multiple_choice')
                        <span class="badge bg-primary"><i class="bi bi-list-ul"></i> Pilihan Ganda</span>
                        @break
                    @case('true_false')
                        <span class="badge bg-warning"><i class="bi bi-check2-circle"></i> Benar/Salah</span>
                        @break
                    @case('essay')
                        <span class="badge bg-success"><i class="bi bi-pencil"></i> Essay</span>
                        @break
                @endswitch
            </div>
        </div>
        <div class="card-body">
            <h6 class="mb-3">{{ $question->question_text }}</h6>

            @if($question->type !== 'essay' && $question->options)
                <div class="row">
                    @foreach($question->options as $key => $option)
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" disabled 
                                    {{ $question->correct_answer == $key ? 'checked' : '' }}>
                                <label class="form-check-label {{ $question->correct_answer == $key ? 'text-success fw-bold' : '' }}">
                                    <strong>{{ $key }}.</strong> {{ $option }}
                                    @if($question->correct_answer == $key)
                                        <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                    @endif
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($question->type === 'essay')
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i> Soal essay - akan dinilai manual oleh penguji
                </div>
            @endif

            <div class="mt-3 pt-3 border-top">
                <small class="text-muted">
                    Jawaban yang benar: <strong class="text-success">{{ $question->correct_answer ?? '-' }}</strong>
                </small>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('cbt.admin.questions.edit', $question) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('cbt.admin.questions.destroy', $question) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus soal ini dari set?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
    @endforeach
</section>
@endsection
