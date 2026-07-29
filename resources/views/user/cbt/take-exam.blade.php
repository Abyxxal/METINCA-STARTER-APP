@extends('layouts.app')

@section('fullscreen', true)

@section('title', 'Kerjakan Ujian')

@push('styles')
<style>
    #main {
        margin-left: 0 !important;
    }
    .timer-display {
        position: sticky;
        top: 10px;
        z-index: 100;
    }
    .question-nav-btn {
        width: 40px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .question-nav-btn.answered {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: white !important;
    }
    .question-nav-btn.current {
        border: 3px solid #0d6efd !important;
    }
    .question-content {
        min-height: 300px;
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $session->exam->title ?? 'Ujian' }}</h3>
                <p class="text-subtitle text-muted">
                    <span class="badge bg-info">{{ $session->exam->skill->name ?? '-' }}</span>
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                {{-- Timer --}}
                <div class="timer-display float-lg-end">
                    <div class="alert alert-warning mb-0 d-inline-flex align-items-center">
                        <i class="bi bi-clock fs-4 me-2"></i>
                        <div>
                            <small>Sisa Waktu</small>
                            <h4 class="mb-0" id="timer">--:--</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="row">
        {{-- Question Area --}}
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body question-content">
                    <div id="question-container">
                        @foreach($session->exam->questions as $index => $question)
                            <div class="question-item" id="question-{{ $index }}" style="{{ $index > 0 ? 'display:none;' : '' }}">
                                <div class="mb-3">
                                    <h5 class="mb-0">Soal {{ $index + 1 }} dari {{ $session->exam->questions->count() }}</h5>
                                </div>
                                
                                <hr>
                                
                                <div class="question-text mb-4">
                                    <p class="fs-5">{!! nl2br(e($question->question_text)) !!}</p>
                                </div>

                                @if($question->type === 'multiple_choice')
                                    <div class="options">
                                        @php
                                            $options = is_string($question->options) ? json_decode($question->options, true) : $question->options;
                                            $existingAnswer = $answers[$question->id] ?? null;
                                        @endphp
                                        @if($options)
                                            @foreach($options as $key => $option)
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="radio" 
                                                        name="answer_{{ $question->id }}" 
                                                        id="option_{{ $question->id }}_{{ $key }}"
                                                        value="{{ $key }}"
                                                        data-question-id="{{ $question->id }}"
                                                        {{ ($existingAnswer && $existingAnswer->selected_answer === $key) ? 'checked' : '' }}
                                                        onchange="saveAnswer({{ $question->id }}, '{{ $key }}')">
                                                    <label class="form-check-label fs-6" for="option_{{ $question->id }}_{{ $key }}">
                                                        <strong>{{ $key }}.</strong> {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @elseif($question->type === 'true_false')
                                    @php $existingAnswer = $answers[$question->id] ?? null; @endphp
                                    <div class="options">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" 
                                                name="answer_{{ $question->id }}" 
                                                id="option_{{ $question->id }}_true"
                                                value="true"
                                                data-question-id="{{ $question->id }}"
                                                {{ ($existingAnswer && $existingAnswer->selected_answer === 'true') ? 'checked' : '' }}
                                                onchange="saveAnswer({{ $question->id }}, 'true')">
                                            <label class="form-check-label fs-6" for="option_{{ $question->id }}_true">
                                                <strong>Benar</strong>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" 
                                                name="answer_{{ $question->id }}" 
                                                id="option_{{ $question->id }}_false"
                                                value="false"
                                                data-question-id="{{ $question->id }}"
                                                {{ ($existingAnswer && $existingAnswer->selected_answer === 'false') ? 'checked' : '' }}
                                                onchange="saveAnswer({{ $question->id }}, 'false')">
                                            <label class="form-check-label fs-6" for="option_{{ $question->id }}_false">
                                                <strong>Salah</strong>
                                            </label>
                                        </div>
                                    </div>
                                @elseif($question->type === 'essay')
                                    @php $existingAnswer = $answers[$question->id] ?? null; @endphp
                                    <div class="form-group">
                                        <label class="form-label mb-2">
                                            <i class="bi bi-pencil-square"></i> Tulis jawaban essay Anda:
                                        </label>
                                        <textarea class="form-control" 
                                            name="answer_{{ $question->id }}"
                                            id="essay_{{ $question->id }}"
                                            rows="8"
                                            placeholder="Tulis jawaban Anda di sini dengan lengkap dan jelas...

Pastikan jawaban Anda mencakup:
- Penjelasan yang detail
- Contoh jika diperlukan
- Kesimpulan"
                                            style="min-height: 200px; font-size: 0.95rem;"
                                            onblur="saveAnswer({{ $question->id }}, this.value)">{{ $existingAnswer->selected_answer ?? '' }}</textarea>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> Jawaban akan tersimpan otomatis saat Anda klik di luar area teks
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Navigation --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-secondary" id="prevBtn" onclick="navigateQuestion(-1)" disabled>
                            <i class="bi bi-arrow-left"></i> Sebelumnya
                        </button>
                        <button class="btn btn-primary" id="nextBtn" onclick="navigateQuestion(1)">
                            Selanjutnya <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-3">
            {{-- Question Navigation --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Navigasi Soal</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2" id="question-nav">
                        @foreach($session->exam->questions as $index => $question)
                            @php $existingAnswer = $answers[$question->id] ?? null; @endphp
                            <button type="button" 
                                class="btn btn-outline-secondary question-nav-btn {{ $existingAnswer ? 'answered' : '' }} {{ $index === 0 ? 'current' : '' }}"
                                id="nav-{{ $index }}"
                                onclick="goToQuestion({{ $index }})">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <span class="btn btn-sm btn-outline-secondary question-nav-btn me-2" style="width:25px;height:25px;"></span>
                        <small>Belum dijawab</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="btn btn-sm btn-success question-nav-btn me-2" style="width:25px;height:25px;"></span>
                        <small>Sudah dijawab</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="btn btn-sm btn-outline-primary question-nav-btn me-2" style="width:25px;height:25px;border-width:3px !important;"></span>
                        <small>Soal aktif</small>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="card">
                <div class="card-body">
                    <p class="mb-2">
                        <strong id="answered-count">0</strong> dari {{ $session->exam->questions->count() }} soal terjawab
                    </p>
                    <form action="{{ route('cbt.employee.submit', $session->id) }}" method="POST" id="submitForm">
                        @csrf
                        <button type="button" class="btn btn-success w-100" onclick="confirmSubmit()">
                            <i class="bi bi-check-circle"></i> Selesai & Kirim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    let currentQuestion = 0;
    const totalQuestions = {{ $session->exam->questions->count() }};
    let remainingSeconds = {{ $session->getRemainingTime() }};
    const sessionId = {{ $session->id }};
    const answeredQuestions = new Set([
        @foreach($answers as $questionId => $answer)
            {{ $questionId }},
        @endforeach
    ]);

    // Timer
    function updateTimer() {
        if (remainingSeconds <= 0) {
            document.getElementById('timer').textContent = '00:00';
            alert('Waktu habis! Ujian akan dikirim otomatis.');
            document.getElementById('submitForm').submit();
            return;
        }

        const hours = Math.floor(remainingSeconds / 3600);
        const minutes = Math.floor((remainingSeconds % 3600) / 60);
        const seconds = remainingSeconds % 60;

        if (hours > 0) {
            document.getElementById('timer').textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
        } else {
            document.getElementById('timer').textContent = 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
        }

        // Warning when less than 5 minutes
        if (remainingSeconds <= 300) {
            document.getElementById('timer').closest('.alert').classList.remove('alert-warning');
            document.getElementById('timer').closest('.alert').classList.add('alert-danger');
        }

        remainingSeconds--;
    }

    setInterval(updateTimer, 1000);
    updateTimer();

    // Navigation
    function navigateQuestion(direction) {
        const newIndex = currentQuestion + direction;
        if (newIndex >= 0 && newIndex < totalQuestions) {
            goToQuestion(newIndex);
        }
    }

    function goToQuestion(index) {
        // Hide current
        document.getElementById('question-' + currentQuestion).style.display = 'none';
        document.getElementById('nav-' + currentQuestion).classList.remove('current');

        // Show new
        currentQuestion = index;
        document.getElementById('question-' + currentQuestion).style.display = 'block';
        document.getElementById('nav-' + currentQuestion).classList.add('current');

        // Update buttons
        document.getElementById('prevBtn').disabled = currentQuestion === 0;
        document.getElementById('nextBtn').disabled = currentQuestion === totalQuestions - 1;
    }

    // Save Answer
    function saveAnswer(questionId, answer) {
        console.log('Saving answer:', questionId, answer);
        
        fetch('{{ url("/cbt/session") }}/' + sessionId + '/save-answer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                question_id: questionId,
                selected_answer: answer
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                answeredQuestions.add(questionId);
                updateAnsweredCount();
                
                // Update nav button
                const navButtons = document.querySelectorAll('.question-nav-btn');
                navButtons.forEach((btn, index) => {
                    const qId = {{ json_encode($session->exam->questions->pluck('id')->toArray()) }}[index];
                    if (answeredQuestions.has(qId)) {
                        btn.classList.add('answered');
                    }
                });
                
                console.log('Answer saved successfully. Total answered:', answeredQuestions.size);
            } else {
                console.error('Failed to save answer:', data);
            }
        })
        .catch(error => {
            console.error('Error saving answer:', error);
            alert('Gagal menyimpan jawaban. Coba lagi.');
        });
    }

    function updateAnsweredCount() {
        document.getElementById('answered-count').textContent = answeredQuestions.size;
    }

    function confirmSubmit() {
        const unanswered = totalQuestions - answeredQuestions.size;
        let message = 'Apakah Anda yakin ingin menyelesaikan ujian?';
        
        if (unanswered > 0) {
            message = 'Masih ada ' + unanswered + ' soal yang belum dijawab. Yakin ingin menyelesaikan ujian?';
        }

        if (confirm(message)) {
            // Collect all answers from radio/textarea inputs and add to form
            const form = document.getElementById('submitForm');
            
            // Clear existing hidden inputs (if any)
            form.querySelectorAll('input[type="hidden"]').forEach(input => {
                if (input.name.startsWith('answers[')) {
                    input.remove();
                }
            });
            
            // Add all selected answers as hidden inputs
            const questionIds = {{ json_encode($session->exam->questions->pluck('id')->toArray()) }};
            
            questionIds.forEach(questionId => {
                let selectedValue = null;
                
                // Check for multiple choice or true/false (radio buttons)
                const radioButton = document.querySelector(`input[name="answer_${questionId}"]:checked`);
                if (radioButton) {
                    selectedValue = radioButton.value;
                }
                
                // Check for essay (textarea)
                const essayField = document.querySelector(`textarea[name="answer_${questionId}"]`);
                if (essayField) {
                    selectedValue = essayField.value;
                }
                
                // Add to form if answer exists
                if (selectedValue !== null && selectedValue !== '') {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `answers[${questionId}]`;
                    hiddenInput.value = selectedValue;
                    form.appendChild(hiddenInput);
                }
            });
            
            // Submit the form
            form.submit();
        }
    }

    // Initial count
    updateAnsweredCount();
</script>
@endpush
@endsection
