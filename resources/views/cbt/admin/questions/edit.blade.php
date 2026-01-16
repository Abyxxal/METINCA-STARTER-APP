@extends('layouts.app')

@section('title', 'Edit Soal')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Soal</h3>
                <p class="text-subtitle text-muted">Edit soal yang ada atau tambah soal baru</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.questions.index') }}">Bank Soal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <form action="{{ route('cbt.admin.questions.update', $question) }}" method="POST" id="questionForm">
        @csrf
        @method('PUT')
        
        {{-- Pengaturan Umum --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Pengaturan Umum</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">Skill <span class="text-danger">*</span></label>
                            <select name="skill_id" class="form-select @error('skill_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Skill --</option>
                                @foreach($skills as $skill)
                                    <option value="{{ $skill->id }}" {{ old('skill_id', $question->skill_id) == $skill->id ? 'selected' : '' }}>
                                        {{ $skill->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('skill_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">Untuk Level <span class="text-danger">*</span></label>
                            <select name="for_level" class="form-select @error('for_level') is-invalid @enderror" required>
                                <option value="">-- Pilih Level --</option>
                                <option value="1" {{ old('for_level', $question->for_level) == 1 ? 'selected' : '' }}>Level 1</option>
                                <option value="2" {{ old('for_level', $question->for_level) == 2 ? 'selected' : '' }}>Level 2</option>
                                <option value="3" {{ old('for_level', $question->for_level) == 3 ? 'selected' : '' }}>Level 3</option>
                                <option value="4" {{ old('for_level', $question->for_level) == 4 ? 'selected' : '' }}>Level 4</option>
                            </select>
                            @error('for_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $question->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $question->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Soal Utama (yang sedang diedit) --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Soal Utama (Sedang Diedit)</h5>
            </div>
            <div class="card-body">

                <div class="form-group mb-3">
                    <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                    <textarea name="question_text" class="form-control @error('question_text') is-invalid @enderror" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
                    @error('question_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe Soal</label>
                        <select name="type" id="mainQuestionType" class="form-select question-type" data-index="main">
                            <option value="multiple_choice" {{ old('type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                            <option value="true_false" {{ old('type', $question->type) == 'true_false' ? 'selected' : '' }}>Benar/Salah</option>
                            <option value="essay" {{ old('type', $question->type) == 'essay' ? 'selected' : '' }}>Essay</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3 correct-answer-wrap" data-index="main">
                        <label class="form-label">Jawaban Benar <span class="text-danger">*</span></label>
                        <select name="correct_answer" id="mainCorrectAnswer" class="form-select correct-answer" data-index="main" {{ $question->type == 'essay' ? '' : 'required' }}>
                            @if($question->type == 'true_false')
                                <option value="A" {{ old('correct_answer', $question->correct_answer) == 'A' ? 'selected' : '' }}>Benar</option>
                                <option value="B" {{ old('correct_answer', $question->correct_answer) == 'B' ? 'selected' : '' }}>Salah</option>
                            @else
                                <option value="A" {{ old('correct_answer', $question->correct_answer) == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ old('correct_answer', $question->correct_answer) == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ old('correct_answer', $question->correct_answer) == 'C' ? 'selected' : '' }}>C</option>
                                <option value="D" {{ old('correct_answer', $question->correct_answer) == 'D' ? 'selected' : '' }}>D</option>
                            @endif
                        </select>
                    </div>
                </div>

                {{-- Options --}}
                <div class="options-wrap" data-index="main" style="{{ $question->type == 'essay' || $question->type == 'true_false' ? 'display:none;' : '' }}">
                    <label class="form-label">Pilihan Jawaban</label>
                    <div class="row">
                        @php $options = old('options', $question->options ?? []); @endphp
                        <div class="col-md-6 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">A</span>
                                <input type="text" name="options[A]" class="form-control" placeholder="Opsi A" value="{{ $options['A'] ?? '' }}" {{ $question->type == 'multiple_choice' ? 'required' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">B</span>
                                <input type="text" name="options[B]" class="form-control" placeholder="Opsi B" value="{{ $options['B'] ?? '' }}" {{ $question->type == 'multiple_choice' ? 'required' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">C</span>
                                <input type="text" name="options[C]" class="form-control" placeholder="Opsi C (opsional)" value="{{ $options['C'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="input-group">
                                <span class="input-group-text">D</span>
                                <input type="text" name="options[D]" class="form-control" placeholder="Opsi D (opsional)" value="{{ $options['D'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Soal Tambahan Baru --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Tambah Soal Baru <span class="badge bg-success ms-2" id="questionCount">0</span></h5>
                <button type="button" class="btn btn-sm btn-success" id="addQuestionBtn">
                    <i class="bi bi-plus-circle"></i> Tambah Soal
                </button>
            </div>
            <div class="card-body">
                <div id="questionsContainer">
                    {{-- Question items will be added here --}}
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Simpan Semua
                    </button>
                    <a href="{{ route('cbt.admin.questions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let questionIndex = 0;
    const container = document.getElementById('questionsContainer');
    const addBtn = document.getElementById('addQuestionBtn');
    const form = document.getElementById('questionForm');
    const countBadge = document.getElementById('questionCount');

    // Main question type handler
    const mainTypeSelect = document.getElementById('mainQuestionType');
    const mainOptionsWrap = document.querySelector('.options-wrap[data-index="main"]');
    const mainCorrectAnswerWrap = document.querySelector('.correct-answer-wrap[data-index="main"]');
    const mainCorrectAnswer = document.getElementById('mainCorrectAnswer');
    
    mainTypeSelect.addEventListener('change', function() {
        const type = this.value;
        const optionInputs = mainOptionsWrap.querySelectorAll('input');
        
        if (type === 'essay') {
            mainOptionsWrap.style.display = 'none';
            mainCorrectAnswerWrap.style.display = 'none';
            mainCorrectAnswer.removeAttribute('required');
            optionInputs.forEach(i => i.removeAttribute('required'));
        } else if (type === 'true_false') {
            mainOptionsWrap.style.display = 'none';
            mainCorrectAnswerWrap.style.display = 'block';
            mainCorrectAnswer.setAttribute('required', 'required');
            mainCorrectAnswer.innerHTML = '<option value="A">Benar</option><option value="B">Salah</option>';
            optionInputs.forEach(i => i.removeAttribute('required'));
        } else {
            mainOptionsWrap.style.display = 'block';
            mainCorrectAnswerWrap.style.display = 'block';
            mainCorrectAnswer.setAttribute('required', 'required');
            mainCorrectAnswer.innerHTML = '<option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>';
            mainOptionsWrap.querySelectorAll('input[name="options[A]"], input[name="options[B]"]').forEach(i => i.setAttribute('required', 'required'));
        }
    });

    function updateCount() {
        const count = container.querySelectorAll('.question-item').length;
        countBadge.textContent = count;
    }

    function createQuestionForm(index) {
        const num = container.querySelectorAll('.question-item').length + 1;
        return `
            <div class="card mb-3 question-item border-start border-success border-4" data-index="${index}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <strong>Soal Baru #${num}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-btn" data-index="${index}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
                <div class="card-body">
                    <!-- Pertanyaan -->
                    <div class="form-group mb-3">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="new_questions[${index}][question_text]" 
                                  class="form-control" 
                                  rows="2" 
                                  placeholder="Ketik pertanyaan..." 
                                  required></textarea>
                    </div>

                    <div class="row">
                        <!-- Tipe Soal -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe Soal</label>
                            <select name="new_questions[${index}][type]" class="form-select question-type" data-index="${index}">
                                <option value="multiple_choice" selected>Pilihan Ganda</option>
                                <option value="true_false">Benar / Salah</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>

                        <!-- Jawaban Benar -->
                        <div class="col-md-6 mb-3 correct-answer-wrap" data-index="${index}">
                            <label class="form-label">Jawaban Benar <span class="text-danger">*</span></label>
                            <select name="new_questions[${index}][correct_answer]" class="form-select correct-answer" data-index="${index}" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>

                    <!-- Options Container -->
                    <div class="options-wrap" data-index="${index}">
                        <label class="form-label">Pilihan Jawaban</label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">A</span>
                                    <input type="text" name="new_questions[${index}][options][A]" class="form-control" placeholder="Opsi A" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">B</span>
                                    <input type="text" name="new_questions[${index}][options][B]" class="form-control" placeholder="Opsi B" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">C</span>
                                    <input type="text" name="new_questions[${index}][options][C]" class="form-control" placeholder="Opsi C (opsional)">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">D</span>
                                    <input type="text" name="new_questions[${index}][options][D]" class="form-control" placeholder="Opsi D (opsional)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function addQuestion() {
        container.insertAdjacentHTML('beforeend', createQuestionForm(questionIndex));
        
        // Attach type change listener
        const typeSelect = container.querySelector(`.question-type[data-index="${questionIndex}"]`);
        const optionsWrap = container.querySelector(`.options-wrap[data-index="${questionIndex}"]`);
        const correctAnswerWrap = container.querySelector(`.correct-answer-wrap[data-index="${questionIndex}"]`);
        const correctAnswer = container.querySelector(`.correct-answer[data-index="${questionIndex}"]`);
        const optionInputs = optionsWrap.querySelectorAll('input[required]');
        
        typeSelect.addEventListener('change', function() {
            const type = this.value;
            if (type === 'essay') {
                optionsWrap.style.display = 'none';
                correctAnswerWrap.style.display = 'none';
                correctAnswer.removeAttribute('required');
                optionInputs.forEach(i => i.removeAttribute('required'));
            } else if (type === 'true_false') {
                optionsWrap.style.display = 'none';
                correctAnswerWrap.style.display = 'block';
                correctAnswer.setAttribute('required', 'required');
                correctAnswer.innerHTML = '<option value="A">Benar</option><option value="B">Salah</option>';
                optionInputs.forEach(i => i.removeAttribute('required'));
            } else {
                optionsWrap.style.display = 'block';
                correctAnswerWrap.style.display = 'block';
                correctAnswer.setAttribute('required', 'required');
                correctAnswer.innerHTML = '<option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>';
                optionInputs.forEach(i => i.setAttribute('required', 'required'));
            }
        });

        // Attach remove listener
        const removeBtn = container.querySelector(`.remove-btn[data-index="${questionIndex}"]`);
        removeBtn.addEventListener('click', function() {
            container.querySelector(`.question-item[data-index="${this.dataset.index}"]`).remove();
            updateCount();
            renumberQuestions();
        });

        questionIndex++;
        updateCount();
    }

    function renumberQuestions() {
        const items = container.querySelectorAll('.question-item');
        items.forEach((item, i) => {
            const header = item.querySelector('.card-header strong');
            if (header) header.textContent = `Soal Baru #${i + 1}`;
        });
    }

    addBtn.addEventListener('click', addQuestion);
});
</script>
@endpush
@endsection
