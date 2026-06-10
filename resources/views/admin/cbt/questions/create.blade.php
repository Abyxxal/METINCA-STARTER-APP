@extends('layouts.app')

@section('title', 'Tambah Soal')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Soal Baru</h3>
                <p class="text-subtitle text-muted">Buat soal untuk bank soal</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.questions.index') }}">Bank Soal</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <form action="{{ route('cbt.admin.questions.store') }}" method="POST" id="questionForm">
        @csrf
        
        {{-- Pengaturan Umum --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Pengaturan Umum</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- 1. Judul Set Soal --}}
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Judul Set Soal</label>
                            <input type="text" name="set_title" class="form-control @error('set_title') is-invalid @enderror" 
                                placeholder="Contoh: Ujian CMM Dasar" value="{{ old('set_title') }}">
                            @error('set_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Opsional, otomatis jika kosong</small>
                        </div>
                    </div>

                    {{-- 2. Divisi --}}
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label">Divisi <span class="text-danger">*</span></label>
                            <select name="division_id" id="divisionSelect" class="form-select @error('division_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
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
                            <select name="target_position" id="targetPositionsSelect" class="form-select" disabled>
                                <option value="">-- Semua Jabatan --</option>
                            </select>
                            <small class="text-muted">Kosongkan untuk semua</small>
                        </div>
                    </div>

                    {{-- 4. Skill --}}
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label class="form-label">Skill <span class="text-danger">*</span></label>
                            <select name="skill_id" id="skillSelect" class="form-select @error('skill_id') is-invalid @enderror" required disabled>
                                <option value="">-- Pilih Skill --</option>
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
                                <option value="1" {{ old('for_level') == 1 ? 'selected' : '' }}>Level 1</option>
                                <option value="2" {{ old('for_level') == 2 ? 'selected' : '' }}>Level 2</option>
                                <option value="3" {{ old('for_level') == 3 ? 'selected' : '' }}>Level 3</option>
                                <option value="4" {{ old('for_level') == 4 ? 'selected' : '' }}>Level 4</option>
                            </select>
                            @error('for_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Soal --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Soal <span class="badge bg-primary ms-2" id="questionCount">0</span></h5>
            </div>
            <div class="card-body">
                <div id="questionsContainer">
                    {{-- Question items will be added here --}}
                </div>

                <button type="button" class="btn btn-success" id="addQuestionBtn">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Soal
                </button>
            </div>
            <div class="card-footer">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Simpan Semua Soal
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
    const divisionSelect = document.getElementById('divisionSelect');
    const skillSelect = document.getElementById('skillSelect');

    // Cascading dropdown: Division → Skill
    divisionSelect.addEventListener('change', function() {
        const divisionId = this.value;
        skillSelect.innerHTML = '<option value="">-- Loading... --</option>';
        skillSelect.disabled = true;
        
        if (divisionId) {
            // Fetch skills for selected division
            fetch(`/api/divisions/${divisionId}/skills`)
                .then(response => response.json())
                .then(data => {
                    skillSelect.innerHTML = '<option value="">-- Pilih Skill --</option>';
                    
                    if (data.success && data.skills.length > 0) {
                        data.skills.forEach(skill => {
                            const option = document.createElement('option');
                            option.value = skill.id;
                            option.textContent = skill.name;
                            skillSelect.appendChild(option);
                        });
                        skillSelect.disabled = false;
                    } else {
                        skillSelect.innerHTML = '<option value="">Tidak ada skill untuk divisi ini</option>';
                        skillSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error loading skills:', error);
                    skillSelect.innerHTML = '<option value="">Error memuat skill</option>';
                    skillSelect.disabled = true;
                });
        } else {
            skillSelect.innerHTML = '<option value="">-- Pilih Divisi Terlebih Dahulu --</option>';
            skillSelect.disabled = true;
        }
    });

    function updateCount() {
        const count = container.querySelectorAll('.question-item').length;
        countBadge.textContent = count;
    }

    function createQuestionForm(index) {
        const num = container.querySelectorAll('.question-item').length + 1;
        return `
            <div class="card mb-3 question-item border-start border-primary border-4" data-index="${index}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                    <strong>Soal #${num}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-btn" data-index="${index}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
                <div class="card-body">
                    <!-- Pertanyaan -->
                    <div class="form-group mb-3">
                        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="questions[${index}][question_text]" 
                                  class="form-control" 
                                  rows="2" 
                                  placeholder="Ketik pertanyaan..." 
                                  required></textarea>
                    </div>

                    <div class="row">
                        <!-- Tipe Soal -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipe Soal</label>
                            <select name="questions[${index}][type]" class="form-select question-type" data-index="${index}">
                                <option value="multiple_choice" selected>Pilihan Ganda</option>
                                <option value="true_false">Benar / Salah</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>

                        <!-- Bobot (hanya untuk Essay) -->
                        <div class="col-md-4 mb-3 default-weight-wrap" data-index="${index}" style="display:none;">
                            <label class="form-label">
                                Bobot Soal
                                <small class="text-muted">(Essay)</small>
                            </label>
                            <input type="number" name="questions[${index}][default_weight]" 
                                class="form-control" 
                                min="1" max="999"
                                placeholder="Contoh: 20"
                                value="">
                            <small class="text-muted">Nilai bobot untuk perhitungan skor essay</small>
                        </div>

                        <!-- Jawaban Benar -->
                        <div class="col-md-4 mb-3 correct-answer-wrap" data-index="${index}">
                            <label class="form-label">Jawaban Benar <span class="text-danger">*</span></label>
                            <select name="questions[${index}][correct_answer]" class="form-select correct-answer" data-index="${index}" required>
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
                                    <input type="text" name="questions[${index}][options][A]" class="form-control" placeholder="Opsi A" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">B</span>
                                    <input type="text" name="questions[${index}][options][B]" class="form-control" placeholder="Opsi B" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">C</span>
                                    <input type="text" name="questions[${index}][options][C]" class="form-control" placeholder="Opsi C (opsional)">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <span class="input-group-text">D</span>
                                    <input type="text" name="questions[${index}][options][D]" class="form-control" placeholder="Opsi D (opsional)">
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
        const defaultWeightWrap = container.querySelector(`.default-weight-wrap[data-index="${questionIndex}"]`);
        const optionInputs = optionsWrap.querySelectorAll('input[required]');
        
        typeSelect.addEventListener('change', function() {
            const type = this.value;
            if (type === 'essay') {
                optionsWrap.style.display = 'none';
                correctAnswerWrap.style.display = 'none';
                correctAnswer.removeAttribute('required');
                defaultWeightWrap.style.display = 'block';
                optionInputs.forEach(i => i.removeAttribute('required'));
            } else if (type === 'true_false') {
                optionsWrap.style.display = 'none';
                correctAnswerWrap.style.display = 'block';
                correctAnswer.setAttribute('required', 'required');
                defaultWeightWrap.style.display = 'none';
                correctAnswer.innerHTML = '<option value="A">Benar</option><option value="B">Salah</option>';
                optionInputs.forEach(i => i.removeAttribute('required'));
            } else {
                optionsWrap.style.display = 'block';
                correctAnswerWrap.style.display = 'block';
                correctAnswer.setAttribute('required', 'required');
                defaultWeightWrap.style.display = 'none';
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
            if (header) header.textContent = `Soal #${i + 1}`;
        });
    }

    addBtn.addEventListener('click', addQuestion);

    form.addEventListener('submit', function(e) {
        const questions = container.querySelectorAll('.question-item');
        if (questions.length === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 soal!');
        }
    });

    // Cascade dropdown: Division → Positions
    const targetPositionsSelect = document.getElementById('targetPositionsSelect');
    
    divisionSelect.addEventListener('change', function() {
        const divisionId = this.value;
        
        // Reset positions dropdown
        targetPositionsSelect.innerHTML = '<option value="" disabled>-- Loading... --</option>';
        targetPositionsSelect.disabled = true;
        
        if (divisionId) {
            // Fetch positions for selected division
            fetch(`/api/positions?division_id=${divisionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        targetPositionsSelect.innerHTML = '<option value="">-- Semua Jabatan (Universal) --</option>';
                        data.data.forEach(position => {
                            const option = document.createElement('option');
                            option.value = position.id;
                            option.textContent = position.name;
                            targetPositionsSelect.appendChild(option);
                        });
                        targetPositionsSelect.disabled = false;
                    } else {
                        targetPositionsSelect.innerHTML = '<option value="">Tidak ada jabatan untuk divisi ini</option>';
                        targetPositionsSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error loading positions:', error);
                    targetPositionsSelect.innerHTML = '<option value="" disabled>Error memuat jabatan</option>';
                    targetPositionsSelect.disabled = true;
                });
        } else {
            targetPositionsSelect.innerHTML = '<option value="">-- Pilih Divisi Terlebih Dahulu --</option>';
            targetPositionsSelect.disabled = true;
        }
    });

    // Add first question on load
    addQuestion();
});
</script>
@endpush
@endsection
