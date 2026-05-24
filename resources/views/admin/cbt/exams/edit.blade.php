@extends('layouts.app')

@section('title', 'Edit Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Ujian</h3>
                <p class="text-subtitle text-muted">{{ $exam->title }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.exams.index') }}">Setup Ujian</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <form action="{{ route('cbt.admin.exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Ujian</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nama Ujian <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                        value="{{ old('title', $exam->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Skill</label>
                                    <input type="text" class="form-control" value="{{ $exam->skill->name ?? '-' }}" disabled>
                                    <small class="text-muted">Skill tidak dapat diubah</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">Target Level <span class="text-danger">*</span></label>
                                    <select name="target_level" class="form-select @error('target_level') is-invalid @enderror" required>
                                        <option value="1" {{ old('target_level', $exam->target_level) == 1 ? 'selected' : '' }}>Level 1</option>
                                        <option value="2" {{ old('target_level', $exam->target_level) == 2 ? 'selected' : '' }}>Level 2</option>
                                        <option value="3" {{ old('target_level', $exam->target_level) == 3 ? 'selected' : '' }}>Level 3</option>
                                        <option value="4" {{ old('target_level', $exam->target_level) == 4 ? 'selected' : '' }}>Level 4</option>
                                    </select>
                                    @error('target_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">KKM <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror" 
                                            value="{{ old('passing_score', $exam->passing_score) }}" min="0" max="100" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('passing_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">Durasi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                            value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" max="480" required>
                                        <span class="input-group-text">menit</span>
                                    </div>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1" 
                                            {{ old('is_published', $exam->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label">Publikasikan</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Questions --}}
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Soal Saat Ini ({{ $exam->questions->count() }})</h4>
                    </div>
                    <div class="card-body">
                        @if($exam->questions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th width="5%"><input type="checkbox" id="selectAllCurrent" checked></th>
                                            <th width="60%">Soal</th>
                                            <th width="15%">Level</th>
                                            <th width="20%">Bobot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($exam->questions as $i => $question)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="current-question-checkbox" 
                                                        name="questions[{{ $i }}][id]" value="{{ $question->id }}" checked>
                                                </td>
                                                <td class="small">{{ Str::limit($question->question_text, 80) }}</td>
                                                <td><span class="badge bg-secondary">L{{ $question->for_level }}</span></td>
                                                <td>
                                                    <input type="number" name="questions[{{ $i }}][weight]" 
                                                        class="form-control form-control-sm weight-input" 
                                                        value="{{ $question->pivot->weight }}" min="1" max="100" style="width: 70px;">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">Belum ada soal dalam ujian ini.</div>
                        @endif

                        {{-- Add More Questions --}}
                        @if($availableQuestions->count() > 0)
                            <hr>
                            <h6>Tambah Soal Baru</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="60%">Soal</th>
                                            <th width="15%">Level</th>
                                            <th width="20%">Bobot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $offset = $exam->questions->count(); @endphp
                                        @foreach($availableQuestions->whereNotIn('id', $exam->questions->pluck('id')) as $i => $question)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="new-question-checkbox" 
                                                        name="questions[{{ $offset + $i }}][id]" value="{{ $question->id }}">
                                                </td>
                                                <td class="small">{{ Str::limit($question->question_text, 80) }}</td>
                                                <td><span class="badge bg-secondary">L{{ $question->for_level }}</span></td>
                                                <td>
                                                    <input type="number" name="questions[{{ $offset + $i }}][weight]" 
                                                        class="form-control form-control-sm weight-input" 
                                                        value="10" min="1" max="100" style="width: 70px;" disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ringkasan</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted">Total Soal Dipilih</td>
                                <td class="fw-bold text-end" id="totalQuestions">{{ $exam->questions->count() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Bobot</td>
                                <td class="fw-bold text-end" id="totalWeight">{{ $exam->getTotalWeight() }}</td>
                            </tr>
                        </table>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg"></i> Update Ujian
                            </button>
                            <a href="{{ route('cbt.admin.exams.show', $exam) }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateSummary() {
        const currentChecked = document.querySelectorAll('.current-question-checkbox:checked');
        const newChecked = document.querySelectorAll('.new-question-checkbox:checked');
        let totalWeight = 0;
        
        currentChecked.forEach(cb => {
            const weight = cb.closest('tr').querySelector('.weight-input');
            totalWeight += parseInt(weight.value) || 0;
        });

        newChecked.forEach(cb => {
            const weight = cb.closest('tr').querySelector('.weight-input');
            totalWeight += parseInt(weight.value) || 0;
        });

        document.getElementById('totalQuestions').textContent = currentChecked.length + newChecked.length;
        document.getElementById('totalWeight').textContent = totalWeight;
    }

    // Select all for current questions
    document.getElementById('selectAllCurrent')?.addEventListener('change', function() {
        document.querySelectorAll('.current-question-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
        updateSummary();
    });

    // Enable/disable weight input when checking new questions
    document.querySelectorAll('.new-question-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            this.closest('tr').querySelector('.weight-input').disabled = !this.checked;
            updateSummary();
        });
    });

    // Update summary on any change
    document.querySelectorAll('.current-question-checkbox, .weight-input').forEach(el => {
        el.addEventListener('change', updateSummary);
    });
});
</script>
@endpush
@endsection
