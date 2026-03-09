@extends('layouts.app')

@section('title', 'Edit Set Soal')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Set Soal</h3>
                <p class="text-subtitle text-muted">{{ $setInfo->set_title }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.questions.index') }}">Bank Soal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Set</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <form action="{{ route('cbt.admin.questions.update-set', $setInfo->question_set_id) }}" method="POST" id="editSetForm">
        @csrf
        @method('PUT')

        {{-- Pengaturan Set --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Pengaturan Set Soal</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- 1. Judul Set Soal --}}
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Judul Set Soal <span class="text-danger">*</span></label>
                            <input type="text" name="set_title" class="form-control @error('set_title') is-invalid @enderror" 
                                value="{{ old('set_title', $setInfo->set_title) }}" required>
                            @error('set_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Nama set soal</small>
                        </div>
                    </div>

                    {{-- 2. Divisi --}}
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Divisi <span class="text-danger">*</span></label>
                            <select name="division_id" id="divisionSelect" class="form-select @error('division_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" 
                                        {{ old('division_id', $setInfo->skill->division_id ?? '') == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }} ({{ $division->department->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('division_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih divisi dulu</small>
                        </div>
                    </div>

                    {{-- 3. Target Jabatan --}}
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Jabatan</label>
                            <select name="target_position" id="targetPositionsSelect" class="form-select" 
                                data-selected-position="{{ $setInfo->positions->isNotEmpty() ? $setInfo->positions->first()->id : '' }}">
                                <option value="">-- Semua Jabatan --</option>
                                @if($setInfo->positions->isNotEmpty())
                                    <option value="{{ $setInfo->positions->first()->id }}" selected>
                                        {{ $setInfo->positions->first()->name }}
                                    </option>
                                @endif
                            </select>
                            <small class="text-muted">Kosongkan untuk semua</small>
                        </div>
                    </div>

                    {{-- 4. Skill --}}
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Skill <span class="text-danger">*</span></label>
                            <select name="skill_id" id="skillSelect" class="form-select @error('skill_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Skill --</option>
                                @foreach($skills as $skill)
                                    <option value="{{ $skill->id }}" data-division-id="{{ $skill->division_id }}" 
                                        {{ old('skill_id', $setInfo->skill_id) == $skill->id ? 'selected' : '' }}>
                                        {{ $skill->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('skill_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Berdasarkan divisi</small>
                        </div>
                    </div>

                    {{-- 5. Level --}}
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select name="for_level" class="form-select @error('for_level') is-invalid @enderror" required>
                                <option value="">-- Level --</option>
                                @for($i = 1; $i <= 4; $i++)
                                    <option value="{{ $i }}" {{ old('for_level', $setInfo->for_level) == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                                @endfor
                            </select>
                            @error('for_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Tingkat kesulitan</small>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Status (full width) --}}
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $setInfo->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $setInfo->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="draft" {{ old('status', $setInfo->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Status set soal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Soal --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">Daftar Soal <span class="badge bg-primary ms-2" id="questionCount">{{ count($questions) }}</span></h5>
            </div>
        </div>

        <div id="questionsContainer">
        @foreach($questions as $index => $question)
        <div class="card mb-3 question-card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Soal #{{ $index + 1 }}</h5>
                <div class="d-flex gap-2">
                    <select name="questions[{{ $index }}][type]" class="form-select form-select-sm type-select" data-index="{{ $index }}" style="width: 150px;">
                        <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                        <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>Benar/Salah</option>
                        <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>Essay</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-danger remove-btn" data-question-id="{{ $question->id }}" data-index="{{ $index }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                
                <div class="form-group mb-3">
                    <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                    <textarea name="questions[{{ $index }}][question_text]" class="form-control" rows="3" required>{{ old("questions.$index.question_text", $question->question_text) }}</textarea>
                </div>

                <div class="options-container" data-index="{{ $index }}">
                    @if($question->type === 'multiple_choice')
                        <div class="row">
                            @foreach(['A', 'B', 'C', 'D'] as $opt)
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Pilihan {{ $opt }}</label>
                                    <input type="text" name="questions[{{ $index }}][options][{{ $opt }}]" 
                                        class="form-control" 
                                        value="{{ old("questions.$index.options.$opt", $question->options[$opt] ?? '') }}">
                                </div>
                            @endforeach
                        </div>
                    @elseif($question->type === 'true_false')
                        <div class="alert alert-info">
                            Pilihan otomatis: A = Benar, B = Salah
                        </div>
                    @endif

                    @if($question->type !== 'essay')
                        <div class="form-group mt-3">
                            <label class="form-label">Jawaban yang Benar <span class="text-danger">*</span></label>
                            <select name="questions[{{ $index }}][correct_answer]" class="form-select answer-select">
                                @if($question->type === 'multiple_choice')
                                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                                        <option value="{{ $opt }}" {{ old("questions.$index.correct_answer", $question->correct_answer) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                @else
                                    <option value="A" {{ old("questions.$index.correct_answer", $question->correct_answer) == 'A' ? 'selected' : '' }}>A (Benar)</option>
                                    <option value="B" {{ old("questions.$index.correct_answer", $question->correct_answer) == 'B' ? 'selected' : '' }}>B (Salah)</option>
                                @endif
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        </div>

        {{-- Tombol Tambah Soal --}}
        <div class="text-center mb-3">
            <button type="button" class="btn btn-success" id="addQuestionBtn">
                <i class="bi bi-plus-circle"></i> Tambah Soal Baru
            </button>
        </div>

        {{-- Action Buttons --}}
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Semua
                    </button>
                    <a href="{{ route('cbt.admin.questions.show-set', $setInfo->question_set_id) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let newQuestionIndex = {{ count($questions) }};
    const container = document.getElementById('questionsContainer');
    const addBtn = document.getElementById('addQuestionBtn');
    const countBadge = document.getElementById('questionCount');

    // Cascading division to skill and position
    const divisionSelect = document.getElementById('divisionSelect');
    const skillSelect = document.getElementById('skillSelect');
    const targetPositionsSelect = document.getElementById('targetPositionsSelect');
    
    // Function to filter skills based on selected division
    function filterSkillsByDivision(divisionId) {
        const allOptions = skillSelect.querySelectorAll('option');
        let visibleCount = 0;
        
        allOptions.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            if (!divisionId || option.dataset.divisionId === divisionId) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });
        
        // If selected skill is now hidden, reset selection
        const selectedOption = skillSelect.options[skillSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.divisionId && 
            selectedOption.dataset.divisionId !== divisionId) {
            skillSelect.value = '';
        }
    }
    
    // Function to load positions based on division
    function loadPositions(divisionId, preserveSelection = true) {
        if (!divisionId) {
            targetPositionsSelect.innerHTML = '<option value="">-- Semua Jabatan --</option>';
            targetPositionsSelect.disabled = true;
            return;
        }
        
        const selectedPositionId = preserveSelection ? 
            (targetPositionsSelect.dataset.selectedPosition || targetPositionsSelect.value) : '';
        
        fetch(`/api/positions?division_id=${divisionId}`)
            .then(response => response.json())
            .then(data => {
                targetPositionsSelect.innerHTML = '<option value="">-- Semua Jabatan --</option>';
                
                if (data.success && data.data.length > 0) {
                    data.data.forEach(position => {
                        const option = document.createElement('option');
                        option.value = position.id;
                        option.textContent = position.name;
                        
                        // Re-select previously selected position
                        if (selectedPositionId && position.id == selectedPositionId) {
                            option.selected = true;
                        }
                        
                        targetPositionsSelect.appendChild(option);
                    });
                    targetPositionsSelect.disabled = false;
                } else {
                    targetPositionsSelect.disabled = true;
                }
            })
            .catch(error => console.error('Error loading positions:', error));
    }
    
    if (divisionSelect) {
        divisionSelect.addEventListener('change', function() {
            const divisionId = this.value;
            filterSkillsByDivision(divisionId);
            loadPositions(divisionId, false);
        });

        // On page load: filter skills and load positions if division is already selected
        const initialDivisionId = divisionSelect.value;
        if (initialDivisionId) {
            filterSkillsByDivision(initialDivisionId);
            loadPositions(initialDivisionId, true);
        }
    }

    // Handle type change for existing questions
    document.querySelectorAll('.type-select').forEach(select => {
        select.addEventListener('change', function() {
            const index = this.dataset.index;
            const type = this.value;
            const container = document.querySelector(`.options-container[data-index="${index}"]`);
            
            updateOptionsContainer(container, index, type);
        });
    });

    // Handle remove button for existing questions
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionId = this.dataset.questionId;
            const card = this.closest('.question-card');
            
            if (confirm('Hapus soal ini? Perubahan akan disimpan setelah klik "Simpan Semua".')) {
                // Mark for deletion by hiding the card and adding delete input
                card.style.display = 'none';
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_questions[]';
                deleteInput.value = questionId;
                card.appendChild(deleteInput);
                updateCount();
            }
        });
    });

    function updateOptionsContainer(container, index, type) {
        if (type === 'multiple_choice') {
            container.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Pilihan A</label>
                        <input type="text" name="questions[${index}][options][A]" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Pilihan B</label>
                        <input type="text" name="questions[${index}][options][B]" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Pilihan C</label>
                        <input type="text" name="questions[${index}][options][C]" class="form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Pilihan D</label>
                        <input type="text" name="questions[${index}][options][D]" class="form-control">
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label class="form-label">Jawaban yang Benar <span class="text-danger">*</span></label>
                    <select name="questions[${index}][correct_answer]" class="form-select" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
            `;
        } else if (type === 'true_false') {
            container.innerHTML = `
                <div class="alert alert-info">Pilihan otomatis: A = Benar, B = Salah</div>
                <div class="form-group mt-3">
                    <label class="form-label">Jawaban yang Benar <span class="text-danger">*</span></label>
                    <select name="questions[${index}][correct_answer]" class="form-select" required>
                        <option value="A">A (Benar)</option>
                        <option value="B">B (Salah)</option>
                    </select>
                </div>
            `;
        } else {
            container.innerHTML = '<div class="alert alert-info">Soal essay - tidak memerlukan pilihan jawaban</div>';
        }
    }

    function updateCount() {
        const count = container.querySelectorAll('.question-card').length - container.querySelectorAll('.question-card[style*="display: none"]').length;
        countBadge.textContent = count;
        renumberQuestions();
    }

    function renumberQuestions() {
        const questions = container.querySelectorAll('.question-card:not([style*="display: none"])');
        questions.forEach((card, i) => {
            const header = card.querySelector('.card-header h5');
            if (header) header.textContent = `Soal #${i + 1}`;
        });
    }

    function createNewQuestion(index) {
        return `
            <div class="card mb-3 question-card border-start border-success border-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Soal #${index + 1}</h5>
                    <div class="d-flex gap-2">
                        <select name="questions[${index}][type]" class="form-select form-select-sm type-select" data-index="${index}" style="width: 150px;">
                            <option value="multiple_choice" selected>Pilihan Ganda</option>
                            <option value="true_false">Benar/Salah</option>
                            <option value="essay">Essay</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-danger remove-btn-new" data-index="${index}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="questions[${index}][id]" value="">
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="questions[${index}][question_text]" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="options-container" data-index="${index}">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Pilihan A</label>
                                <input type="text" name="questions[${index}][options][A]" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Pilihan B</label>
                                <input type="text" name="questions[${index}][options][B]" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Pilihan C</label>
                                <input type="text" name="questions[${index}][options][C]" class="form-control">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Pilihan D</label>
                                <input type="text" name="questions[${index}][options][D]" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Jawaban yang Benar <span class="text-danger">*</span></label>
                            <select name="questions[${index}][correct_answer]" class="form-select" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    addBtn.addEventListener('click', function() {
        container.insertAdjacentHTML('beforeend', createNewQuestion(newQuestionIndex));
        
        // Attach event listeners to new question
        const newTypeSelect = container.querySelector(`.type-select[data-index="${newQuestionIndex}"]`);
        if (newTypeSelect) {
            newTypeSelect.addEventListener('change', function() {
                const index = this.dataset.index;
                const type = this.value;
                const optContainer = document.querySelector(`.options-container[data-index="${index}"]`);
                updateOptionsContainer(optContainer, index, type);
            });
        }

        // Attach remove button listener
        const removeBtn = container.querySelector(`.remove-btn-new[data-index="${newQuestionIndex}"]`);
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (confirm('Hapus soal ini?')) {
                    this.closest('.question-card').remove();
                    updateCount();
                }
            });
        }

        newQuestionIndex++;
        updateCount();
    });
});
</script>
@endpush
@endsection
