@extends('layouts.app')

@section('title', 'Buat Ujian Baru')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Buat Ujian Baru</h3>
                <p class="text-subtitle text-muted">Konfigurasi ujian kompetensi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.exams.index') }}">Setup Ujian</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buat Baru</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <form action="{{ route('cbt.admin.exams.store') }}" method="POST">
        @csrf
        
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
                                        value="{{ old('title') }}" placeholder="Contoh: CMM Level 2 Upgrade Test" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Divisi <span class="text-danger">*</span></label>
                                    <select id="divisionSelect" class="form-select" required>
                                        <option value="">-- Pilih Divisi --</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}">
                                                {{ $division->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Skill <span class="text-danger">*</span></label>
                                    <select name="skill_id" id="skillSelect" class="form-select @error('skill_id') is-invalid @enderror" required disabled>
                                        <option value="">-- Pilih Divisi Terlebih Dahulu --</option>
                                    </select>
                                    @error('skill_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" 
                                placeholder="Deskripsi singkat tentang ujian ini...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">Target Level <span class="text-danger">*</span></label>
                                    <select name="target_level" id="targetLevel" class="form-select @error('target_level') is-invalid @enderror" required>
                                        <option value="1" {{ old('target_level') == 1 ? 'selected' : '' }}>Level 1 (Novice)</option>
                                        <option value="2" {{ old('target_level', 2) == 2 ? 'selected' : '' }}>Level 2 (Competent)</option>
                                        <option value="3" {{ old('target_level') == 3 ? 'selected' : '' }}>Level 3 (Proficient)</option>
                                        <option value="4" {{ old('target_level') == 4 ? 'selected' : '' }}>Level 4 (Expert)</option>
                                    </select>
                                    @error('target_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label">KKM (Passing Score) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror" 
                                            value="{{ old('passing_score', 80) }}" min="0" max="100" required>
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
                                            value="{{ old('duration_minutes', 60) }}" min="1" max="480" required>
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
                                            {{ old('is_published') ? 'checked' : '' }}>
                                        <label class="form-check-label">Publikasikan</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Questions Selection --}}
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pilih Soal</h4>
                    </div>
                    <div class="card-body">
                        <div id="questionsContainer">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                Pilih Skill terlebih dahulu untuk melihat soal yang tersedia.
                            </div>
                        </div>
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
                                <td class="fw-bold text-end" id="totalQuestions">0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Bobot</td>
                                <td class="fw-bold text-end" id="totalWeight">0</td>
                            </tr>
                        </table>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg"></i> Simpan Ujian
                            </button>
                            <a href="{{ route('cbt.admin.exams.index') }}" class="btn btn-secondary">
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
    const divisionSelect = document.getElementById('divisionSelect');
    const skillSelect = document.getElementById('skillSelect');
    const targetLevel = document.getElementById('targetLevel');
    const questionsContainer = document.getElementById('questionsContainer');
    
    // Preload question sets data
    const allQuestionSets = @json($questionSets);
    
    console.log('Question Sets Data:', allQuestionSets);

    // Handle division change to load skills
    divisionSelect.addEventListener('change', function() {
        const divisionId = this.value;
        
        if (!divisionId) {
            skillSelect.innerHTML = '<option value="">-- Pilih Divisi Terlebih Dahulu --</option>';
            skillSelect.disabled = true;
            questionsContainer.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> Pilih Skill terlebih dahulu untuk melihat soal yang tersedia.</div>';
            updateSummary();
            return;
        }

        // Fetch skills by division
        fetch(`/api/divisions/${divisionId}/skills`)
            .then(response => response.json())
            .then(data => {
                skillSelect.innerHTML = '<option value="">-- Pilih Skill --</option>';
                
                if (data.skills && data.skills.length > 0) {
                    data.skills.forEach(skill => {
                        const option = document.createElement('option');
                        option.value = skill.id;
                        option.textContent = skill.name;
                        skillSelect.appendChild(option);
                    });
                    skillSelect.disabled = false;
                } else {
                    skillSelect.innerHTML = '<option value="">-- Tidak ada skill di divisi ini --</option>';
                    skillSelect.disabled = true;
                }
                
                // Reset questions container
                questionsContainer.innerHTML = '';
            })
            .catch(error => {
                console.error('Error loading skills:', error);
                skillSelect.innerHTML = '<option value="">-- Error loading skills --</option>';
                skillSelect.disabled = true;
            });
    });

    function loadQuestions() {
        const skillId = skillSelect.value;
        const level = parseInt(targetLevel.value);
        
        console.log('Loading questions for skill:', skillId, 'level:', level);
        
        if (!skillId) {
            questionsContainer.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Pilih Skill terlebih dahulu untuk melihat set soal yang tersedia.
                </div>
            `;
            return;
        }

        const questionSets = allQuestionSets[skillId] || [];
        const filteredSets = questionSets.filter(s => s.for_level <= level);
        
        if (filteredSets.length === 0) {
            questionsContainer.innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Tidak ada set soal untuk skill dan level yang dipilih. <a href="{{ route('cbt.admin.questions.create') }}">Buat soal baru</a>.
                </div>
            `;
            return;
        }

        let html = '<p class="text-muted mb-3"><i class="bi bi-info-circle"></i> Pilih set soal yang akan digunakan untuk ujian ini</p>';
        html += '<div class="list-group">';

        filteredSets.forEach((set, index) => {
            html += `<label class="list-group-item d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-3 set-checkbox" 
                    data-set-id="${set.question_set_id}" 
                    data-count="${set.question_count}"
                    value="${set.question_set_id}">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${set.set_title}</h6>
                    <small class="text-muted">
                        <span class="badge bg-secondary">Level ${set.for_level}</span>
                        <span class="badge bg-primary">${set.question_count} soal</span>
                    </small>
                </div>
                <div class="d-flex align-items-center ms-3">
                    <input type="number" class="form-control form-control-sm weight-per-question" 
                        placeholder="Bobot" value="10" min="1" max="100" style="width: 80px;" disabled>
                    <small class="text-muted ms-2">poin/soal</small>
                </div>
            </label>`;
        });

        html += '</div>';
        questionsContainer.innerHTML = html;

        // Fetch questions for each set when checked and add to form
        document.querySelectorAll('.set-checkbox').forEach(checkbox => {
            const weightInput = checkbox.closest('label').querySelector('.weight-per-question');
            
            checkbox.addEventListener('change', function() {
                weightInput.disabled = !this.checked;
                
                if (this.checked) {
                    // Fetch actual questions from this set
                    fetchQuestionsFromSet(this.dataset.setId, weightInput.value);
                } else {
                    // Remove questions from this set
                    removeQuestionsFromSet(this.dataset.setId);
                }
                
                updateSummary();
            });

            weightInput.addEventListener('change', function() {
                if (checkbox.checked) {
                    updateWeightForSet(checkbox.dataset.setId, this.value);
                    updateSummary();
                }
            });
        });
    }

    function fetchQuestionsFromSet(questionSetId, weight) {
        fetch(`/api/questions/set/${questionSetId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.questions) {
                    let index = document.querySelectorAll('input[name^="questions"][name$="[id]"]').length;
                    
                    data.questions.forEach(q => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `questions[${index}][id]`;
                        input.value = q.id;
                        input.dataset.setId = questionSetId;
                        questionsContainer.appendChild(input);
                        
                        const weightInput = document.createElement('input');
                        weightInput.type = 'hidden';
                        weightInput.name = `questions[${index}][weight]`;
                        weightInput.value = weight;
                        weightInput.dataset.setId = questionSetId;
                        weightInput.className = 'weight-for-set';
                        questionsContainer.appendChild(weightInput);
                        
                        index++;
                    });
                    
                    updateSummary();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function removeQuestionsFromSet(questionSetId) {
        document.querySelectorAll(`input[data-set-id="${questionSetId}"]`).forEach(input => {
            input.remove();
        });
        updateSummary();
    }

    function updateWeightForSet(questionSetId, newWeight) {
        document.querySelectorAll(`input[name$="[weight]"][data-set-id="${questionSetId}"]`).forEach(input => {
            input.value = newWeight;
        });
    }

    function updateSummary() {
        const totalQuestions = document.querySelectorAll('input[name$="[id]"][type="hidden"]').length;
        let totalWeight = 0;
        
        document.querySelectorAll('input[name$="[weight]"][type="hidden"]').forEach(input => {
            totalWeight += parseInt(input.value) || 0;
        });

        document.getElementById('totalQuestions').textContent = totalQuestions;
        document.getElementById('totalWeight').textContent = totalWeight;
    }

    skillSelect.addEventListener('change', loadQuestions);
    targetLevel.addEventListener('change', loadQuestions);
});
</script>
@endpush
@endsection
