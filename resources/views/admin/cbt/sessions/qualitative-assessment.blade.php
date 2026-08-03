@extends('layouts.app')

@push('styles')
<style>
:root {
  --bg: #f4f7fb;
  --surface: #ffffff;
  --surface-soft: #f8fafc;
  --text: #172033;
  --muted: #6b7280;
  --line: #e5e7eb;
  --primary: #2563eb;
  --primary-soft: #eff6ff;
  --success: #16803c;
  --success-soft: #ecfdf3;
  --warning: #b45309;
  --warning-soft: #fff7ed;
  --danger: #b42318;
  --danger-soft: #fff1f0;
  --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
  --radius: 16px;
}

body {
  background: var(--bg) !important;
}

.qa-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 24px;
  margin-bottom: 22px;
}

.qa-breadcrumb {
  color: var(--muted);
  font-size: 13px;
  margin-bottom: 8px;
}

.qa-breadcrumb a {
  color: var(--primary);
  text-decoration: none;
}

.qa-header h1 {
  margin: 0;
  font-size: 28px;
  line-height: 1.2;
  color: var(--text);
}

.qa-subtitle {
  margin: 8px 0 0;
  color: var(--muted);
  font-size: 14px;
}

.qa-manager {
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--surface);
  padding: 9px 12px;
  border: 1px solid var(--line);
  border-radius: 12px;
  white-space: nowrap;
}

.qa-avatar {
  width: 38px;
  height: 38px;
  display: grid;
  place-items: center;
  border-radius: 50%;
  background: #dbeafe;
  color: #1d4ed8;
  font-weight: 800;
  font-size: 14px;
}

.qa-manager strong,
.qa-manager span {
  display: block;
}

.qa-manager strong {
  font-size: 13px;
}

.qa-manager span {
  margin-top: 2px;
  color: var(--muted);
  font-size: 12px;
}

/* Employee card */
.employee-card {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 20px;
  align-items: center;
  padding: 22px;
  margin-bottom: 20px;
  background: var(--surface);
  border: 1px solid var(--line);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}

.employee-main {
  display: flex;
  align-items: center;
  gap: 16px;
}

.employee-avatar {
  width: 62px;
  height: 62px;
  display: grid;
  place-items: center;
  border-radius: 16px;
  background: #e0e7ff;
  color: #3730a3;
  font-size: 20px;
  font-weight: 800;
  flex: 0 0 62px;
}

.employee-main h2 {
  margin: 0 0 5px;
  font-size: 20px;
}

.employee-main p {
  margin: 0;
  color: var(--muted);
  font-size: 13px;
}

.status-wrap {
  text-align: right;
}

.status-label {
  display: block;
  margin-bottom: 7px;
  color: var(--muted);
  font-size: 12px;
}

/* Grid */
.qa-grid {
  display: grid;
  grid-template-columns: 360px minmax(0, 1fr);
  gap: 20px;
  align-items: start;
}

/* Card panel */
.qa-card {
  background: var(--surface);
  border: 1px solid var(--line);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 22px;
}

.qa-card + .qa-card {
  margin-top: 20px;
}

.section-title {
  margin: 0 0 5px;
  font-size: 17px;
  color: var(--text);
}

.section-desc {
  margin: 0 0 20px;
  color: var(--muted);
  font-size: 13px;
  line-height: 1.6;
}

/* Score box */
.score-box {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 18px;
  border: 1px solid var(--line);
  border-radius: 14px;
  background: var(--surface-soft);
}

.score-ring {
  --score: 85%;
  position: relative;
  width: 104px;
  height: 104px;
  flex: 0 0 104px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  background: conic-gradient(var(--primary) var(--score), #dbe3ef 0);
}

.score-ring::after {
  content: "";
  position: absolute;
  inset: 10px;
  border-radius: 50%;
  background: #ffffff;
}

.score-ring strong,
.score-ring span {
  position: relative;
  z-index: 1;
  display: block;
  text-align: center;
}

.score-ring strong {
  font-size: 28px;
}

.score-ring span {
  color: var(--muted);
  font-size: 11px;
}

.score-info h3 {
  margin: 0 0 7px;
  font-size: 15px;
}

.score-info p {
  margin: 4px 0;
  color: var(--muted);
  font-size: 12px;
}

/* Detail list */
.detail-list {
  margin-top: 16px;
  border-top: 1px solid var(--line);
}

.detail-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid var(--line);
  font-size: 13px;
}

.detail-row span:first-child {
  color: var(--muted);
}

/* Badge */
.qa-badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 8px 11px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
}

.qa-badge.success {
  background: var(--success-soft);
  color: var(--success);
}

.qa-badge.warning {
  background: var(--warning-soft);
  color: var(--warning);
}

.qa-badge.danger {
  background: var(--danger-soft);
  color: var(--danger);
}

.qa-badge.secondary {
  background: #f3f4f6;
  color: var(--muted);
}

.qa-badge.primary {
  background: var(--primary-soft);
  color: var(--primary);
}

/* Method grid */
.method-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 20px;
}

.method-option {
  position: relative;
}

.method-option input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.method-option label {
  min-height: 74px;
  display: grid;
  place-items: center;
  padding: 12px;
  text-align: center;
  border: 1px solid var(--line);
  border-radius: 12px;
  background: #ffffff;
  color: var(--muted);
  cursor: pointer;
  font-size: 13px;
  font-weight: 700;
  transition: all 0.15s;
}

.method-option input:checked + label {
  border-color: var(--primary);
  background: var(--primary-soft);
  color: var(--primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
}

/* Criteria */
.criteria {
  display: grid;
  gap: 13px;
}

.criterion {
  padding: 16px;
  border: 1px solid var(--line);
  border-radius: 14px;
}

.criterion-head {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}

.criterion-head strong {
  font-size: 14px;
}

.criterion-head span {
  color: var(--muted);
  font-size: 12px;
  white-space: nowrap;
}

.choice-group {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.choice {
  position: relative;
}

.choice input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.choice label {
  display: block;
  padding: 10px 8px;
  border: 1px solid var(--line);
  border-radius: 10px;
  text-align: center;
  cursor: pointer;
  color: var(--muted);
  font-size: 12px;
  font-weight: 700;
  transition: all 0.15s;
}

.choice.good input:checked + label {
  border-color: #34a853;
  background: var(--success-soft);
  color: var(--success);
}

.choice.mid input:checked + label {
  border-color: #f59e0b;
  background: var(--warning-soft);
  color: var(--warning);
}

.choice.bad input:checked + label {
  border-color: #ef4444;
  background: var(--danger-soft);
  color: var(--danger);
}

/* Form grid */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-top: 20px;
}

.form-grid .field.full {
  grid-column: 1 / -1;
}

.field label {
  display: block;
  margin-bottom: 7px;
  font-size: 12px;
  font-weight: 700;
}

.field select,
.field input,
.field textarea {
  width: 100%;
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 11px 12px;
  color: var(--text);
  background: #ffffff;
  outline: none;
  font-size: 14px;
}

.field textarea {
  min-height: 105px;
  resize: vertical;
}

.field select:focus,
.field input:focus,
.field textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Decision box */
.decision-box {
  margin-top: 20px;
  padding: 17px;
  border: 1px dashed #bfdbfe;
  border-radius: 14px;
  background: #f8fbff;
}

.decision-box h3 {
  margin: 0 0 6px;
  font-size: 14px;
}

.decision-box p {
  margin: 0;
  color: var(--muted);
  font-size: 12px;
  line-height: 1.6;
}

/* Actions */
.qa-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid var(--line);
}

.qa-btn {
  border: 0;
  border-radius: 10px;
  padding: 11px 16px;
  cursor: pointer;
  font-weight: 800;
  font-size: 13px;
  transition: all 0.15s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.qa-btn.secondary {
  background: #ffffff;
  color: var(--text);
  border: 1px solid var(--line);
}

.qa-btn.secondary:hover {
  background: #f9fafb;
}

.qa-btn.danger {
  background: var(--danger-soft);
  color: var(--danger);
  border: 1px solid #fecaca;
}

.qa-btn.danger:hover {
  background: #fee2e2;
}

.qa-btn.primary {
  background: var(--primary);
  color: #ffffff;
}

.qa-btn.primary:hover {
  background: #1d4ed8;
}

/* Alert */
.qa-alert {
  padding: 14px 16px;
  border-radius: 12px;
  font-size: 13px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.qa-alert.success {
  background: var(--success-soft);
  color: var(--success);
  border: 1px solid #bbf7d0;
}

@media (max-width: 1050px) {
  .qa-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 700px) {
  .qa-header {
    flex-direction: column;
  }

  .employee-card {
    grid-template-columns: 1fr;
  }

  .status-wrap {
    text-align: left;
  }

  .method-grid,
  .choice-group,
  .form-grid {
    grid-template-columns: 1fr;
  }

  .qa-actions {
    flex-direction: column-reverse;
  }

  .qa-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
@endpush

@section('content')
@php
    $assessment = $session->managerAssessment;
    $examType = $session->getExamType();
    $hasMc = $session->hasMcQuestions();
    $hasEssay = $session->hasEssayQuestions();
    $mcScore = $session->getMcScore();
    $essayScore = $session->getEssayScore();
    $mcTotal = $session->getMcTotalWeight();
    $essayTotal = $session->getEssayTotalWeight();
    $initials = '';
    $empName = $session->employee->name ?? $session->employee_nik;
    $words = explode(' ', $empName);
    foreach ($words as $w) { $initials .= strtoupper(substr($w, 0, 1)); }
    $initials = substr($initials, 0, 2);

    $managerInitials = '';
    $mName = Auth::user()->name ?? '';
    $mWords = explode(' ', $mName);
    foreach ($mWords as $w) { $managerInitials .= strtoupper(substr($w, 0, 1)); }
    $managerInitials = substr($managerInitials, 0, 2);
@endphp

<div class="qa-header">
    <div>
        <div class="qa-breadcrumb">
            <a href="{{ route('cbt.admin.sessions.pending-approval') }}">Persetujuan Level</a> / Detail Pengajuan
        </div>
        <h1>Validasi Kompetensi Karyawan</h1>
        <p class="qa-subtitle">Tinjau hasil kuantitatif dan lakukan penilaian kualitatif sebelum menentukan keputusan akhir.</p>
    </div>

    <div class="qa-manager">
        <div class="qa-avatar">{{ $managerInitials }}</div>
        <div>
            <strong>{{ Auth::user()->name }}</strong>
            <span>Manager</span>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="qa-alert success">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
    </div>
@endif

<section class="employee-card">
    <div class="employee-main">
        <div class="employee-avatar">{{ $initials }}</div>
        <div>
            <h2>{{ $empName }}</h2>
            <p>NIK: {{ $session->employee_nik }} 
                @if($session->employee->division)
                    &middot; {{ $session->employee->division->name ?? '-' }}
                @endif
                @if($session->employee->position)
                    &middot; {{ $session->employee->position->name ?? '-' }}
                @endif
            </p>
            <p style="margin-top:5px;">
                Pengajuan: 
                @php
                    $currentLevel = $session->employee->competencies()
                        ->where('skill_id', $session->exam->skill_id)
                        ->first()?->level ?? 0;
                @endphp
                Level {{ $currentLevel }} → Level {{ $session->exam->target_level }}
            </p>
        </div>
    </div>

    <div class="status-wrap">
        <span class="status-label">Status Pengajuan</span>
        <span class="qa-badge warning">● Menunggu Validasi Kualitatif</span>
    </div>
</section>

<div class="qa-grid">
    <aside>
        {{-- Hasil Kuantitatif --}}
        <div class="qa-card">
            <h2 class="section-title">Hasil Kuantitatif</h2>
            <p class="section-desc">Nilai diperoleh dari ujian kompetensi dan dihitung oleh sistem.</p>

            <div class="score-box">
                <div class="score-ring" style="--score: {{ $session->score }}%;">
                    <div>
                        <strong>{{ $session->score }}</strong>
                        <span>dari 100</span>
                    </div>
                </div>
                <div class="score-info">
                    @if($session->score >= $session->exam->passing_score)
                        <h3>Memenuhi Nilai Minimum</h3>
                        <span class="qa-badge success">✓ Lulus Threshold</span>
                    @else
                        <h3>Belum Memenuhi Nilai Minimum</h3>
                        <span class="qa-badge danger">✗ Tidak Lulus Threshold</span>
                    @endif
                    <p>Threshold kompetensi: {{ $session->exam->passing_score }}</p>
                </div>
            </div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Nilai Pilihan Ganda</span>
                    <strong>{{ $hasMc ? $mcScore : 'Tidak Digunakan' }}</strong>
                </div>
                <div class="detail-row">
                    <span>Nilai Esai</span>
                    <strong>{{ $hasEssay ? $essayScore : 'Tidak Digunakan' }}</strong>
                </div>
                <div class="detail-row">
                    <span>Nilai Akhir</span>
                    <strong>{{ $session->score }}</strong>
                </div>
                <div class="detail-row">
                    <span>Tipe Ujian</span>
                    <strong>{{ $session->getExamTypeLabel() }}</strong>
                </div>
                <div class="detail-row">
                    <span>Tanggal Ujian</span>
                    <strong>{{ $session->finished_at?->format('d F Y') ?? $session->verified_at?->format('d F Y') ?? '-' }}</strong>
                </div>
                <div class="detail-row">
                    <span>Kompetensi</span>
                    <strong>{{ $session->exam->skill->name ?? '-' }}</strong>
                </div>
            </div>
        </div>

        {{-- Ringkasan Keputusan --}}
        <div class="qa-card">
            <h2 class="section-title">Ringkasan Keputusan</h2>
            <p class="section-desc">Status akhir diperoleh dari gabungan hasil kuantitatif dan validasi kualitatif.</p>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Kuantitatif</span>
                    @if($session->score >= $session->exam->passing_score)
                        <span class="qa-badge success">Memenuhi</span>
                    @else
                        <span class="qa-badge danger">Tidak Memenuhi</span>
                    @endif
                </div>
                <div class="detail-row">
                    <span>Kualitatif</span>
                    @php $overall = $assessment?->getOverallResult(); @endphp
                    @if($overall === 'memenuhi')
                        <span class="qa-badge success">Memenuhi</span>
                    @elseif($overall === 'perlu_perbaikan')
                        <span class="qa-badge warning">Perlu Perbaikan</span>
                    @elseif($overall === 'tidak_memenuhi')
                        <span class="qa-badge danger">Tidak Memenuhi</span>
                    @else
                        <span class="qa-badge secondary">Belum Dinilai</span>
                    @endif
                </div>
                <div class="detail-row">
                    <span>Level Akhir</span>
                    <strong>Menunggu Keputusan</strong>
                </div>
            </div>
        </div>
    </aside>

    {{-- Penilaian Kualitatif --}}
    <div class="qa-card">
        <h2 class="section-title">Penilaian Kualitatif Manager</h2>
        <p class="section-desc">
            Gunakan hasil wawancara dan/atau observasi kerja sebagai dasar persetujuan kenaikan level.
        </p>

        <form method="POST" id="assessmentForm">
            @csrf

            {{-- Metode Verifikasi --}}
            <h3 style="font-size:14px; margin:0 0 10px;">Metode Verifikasi</h3>
            <div class="method-grid">
                @foreach(['interview' => 'Wawancara Kompetensi', 'observation' => 'Observasi Kerja', 'both' => 'Wawancara + Observasi'] as $val => $label)
                    <div class="method-option">
                        <input type="radio" name="assessment_method" id="method_{{ $val }}" value="{{ $val }}"
                            {{ old('assessment_method', $assessment?->assessment_method) === $val ? 'checked' : '' }}>
                        <label for="method_{{ $val }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>

            {{-- 5 Kriteria --}}
            <div class="criteria">
                @php
                    $criteriaFields = [
                        'sop_understanding' => ['label' => 'Pemahaman dan penerapan SOP', 'num' => 1],
                        'competency_application' => ['label' => 'Kemampuan menerapkan kompetensi di tempat kerja', 'num' => 2],
                        'independence' => ['label' => 'Kemandirian dalam menjalankan pekerjaan', 'num' => 3],
                        'problem_solving' => ['label' => 'Kemampuan menyelesaikan masalah', 'num' => 4],
                        'readiness' => ['label' => 'Kesiapan menjalankan tanggung jawab level berikutnya', 'num' => 5],
                    ];
                    $choices = [
                        'memenuhi' => ['label' => 'Memenuhi', 'class' => 'good'],
                        'perlu_perbaikan' => ['label' => 'Perlu Perbaikan', 'class' => 'mid'],
                        'tidak_memenuhi' => ['label' => 'Tidak Memenuhi', 'class' => 'bad'],
                    ];
                @endphp

                @foreach($criteriaFields as $field => $info)
                    <div class="criterion">
                        <div class="criterion-head">
                            <strong>{{ $info['label'] }}</strong>
                            <span>Kriteria {{ $info['num'] }}</span>
                        </div>
                        <div class="choice-group">
                            @foreach($choices as $val => $choice)
                                <div class="choice {{ $choice['class'] }}">
                                    <input type="radio" name="{{ $field }}" id="{{ $field }}_{{ $val }}" value="{{ $val }}"
                                        {{ old($field, $assessment?->$field) === $val ? 'checked' : '' }}>
                                    <label for="{{ $field }}_{{ $val }}">{{ $choice['label'] }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Rekomendasi Otomatis --}}
            <div class="qa-recommendation" id="qaRecommendation" style="display:none;">
                <div class="recommendation-badge" id="recommendationBadge"></div>
            </div>

            {{-- Form fields --}}
            <div class="form-grid">
                <div class="field">
                    <label for="verification_date">Tanggal Verifikasi</label>
                    <input type="date" name="verification_date" id="verification_date"
                        value="{{ old('verification_date', $assessment?->verification_date?->format('Y-m-d')) }}">
                </div>

                <div class="field full">
                    <label for="manager_notes">Catatan Manager</label>
                    <textarea name="manager_notes" id="manager_notes" placeholder="Contoh: Karyawan memahami materi, tetapi masih memerlukan pendampingan...">{{ old('manager_notes', $assessment?->manager_notes) }}</textarea>
                </div>
            </div>

            {{-- Decision box --}}
            <div class="decision-box">
                <h3>Ketentuan Keputusan</h3>
                <p>
                    Pilih <strong>Setujui</strong> apabila hasil kuantitatif memenuhi threshold dan seluruh
                    kriteria utama dinilai memenuhi. Pilih <strong>Tolak</strong> apabila karyawan belum memenuhi kompetensi
                    yang dipersyaratkan untuk level berikutnya.
                </p>
            </div>

            {{-- Actions --}}
            <div class="qa-actions">
                <a href="{{ route('cbt.admin.sessions.pending-approval') }}" class="qa-btn secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="qa-btn danger" formaction="{{ route('cbt.admin.sessions.reject-level', $session) }}" id="btnTolak">
                    <i class="bi bi-x-circle"></i> Tolak
                </button>
                <button type="submit" class="qa-btn primary" formaction="{{ route('cbt.admin.sessions.approve-level', $session) }}" id="btnSetujui">
                    <i class="bi bi-check-circle"></i> Setujui
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
.qa-recommendation {
    margin: 16px 0;
}
.recommendation-badge {
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.recommendation-badge small {
    font-weight: 400;
    font-size: 13px;
    opacity: 0.9;
}
.recommendation-badge.warning {
    background: var(--warning-soft, #fff7ed);
    color: var(--warning, #b45309);
    border: 1px solid #fed7aa;
}
.recommendation-badge.danger {
    background: var(--danger-soft, #fff1f0);
    color: var(--danger, #b42318);
    border: 1px solid #fecaca;
}
</style>
<script>
function countTidakMemenuhi() {
    const fields = ['sop_understanding', 'competency_application', 'independence', 'problem_solving', 'readiness'];
    let count = 0;
    fields.forEach(function(f) {
        var selected = document.querySelector('input[name="' + f + '"]:checked');
        if (selected && selected.value === 'tidak_memenuhi') count++;
    });
    return count;
}

function updateRecommendation() {
    var count = countTidakMemenuhi();
    var badge = document.getElementById('recommendationBadge');
    var container = document.getElementById('qaRecommendation');

    if (count === 0) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    if (count === 1) {
        badge.className = 'recommendation-badge warning';
        badge.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Setujui dengan Catatan' +
            '<small>1 dari 5 kriteria Tidak Memenuhi. Catatan manager wajib diisi.</small>';
    } else {
        badge.className = 'recommendation-badge danger';
        badge.innerHTML = '<i class="bi bi-x-octagon"></i> Pertimbangkan Ulang' +
            '<small>' + count + ' dari 5 kriteria Tidak Memenuhi. Catatan manager wajib diisi.</small>';
    }
}

document.querySelectorAll('.choice-group input[type="radio"]').forEach(function(r) {
    r.addEventListener('change', updateRecommendation);
});

document.getElementById('btnSetujui')?.addEventListener('click', function(e) {
    var count = countTidakMemenuhi();
    var notes = document.getElementById('manager_notes')?.value?.trim();

    if (count >= 1 && !notes) {
        alert('Catatan Manager wajib diisi karena ada kriteria yang Tidak Memenuhi.');
        e.preventDefault();
        return;
    }

    var msg = 'Yakin ingin menyetujui kenaikan level karyawan ini?';
    if (count >= 2) {
        msg = count + ' dari 5 kriteria Tidak Memenuhi. Yakin tetap ingin menyetujui kenaikan level?';
    } else if (count === 1) {
        msg = 'Ada 1 kriteria yang Tidak Memenuhi. Yakin ingin menyetujui dengan catatan?';
    }

    if (!confirm(msg)) {
        e.preventDefault();
    }
});

document.getElementById('btnTolak')?.addEventListener('click', function(e) {
    var notes = document.getElementById('manager_notes')?.value?.trim();
    if (!notes) {
        alert('Alasan penolakan wajib diisi pada Catatan Manager.');
        e.preventDefault();
    } else if (!confirm('Yakin ingin menolak kenaikan level karyawan ini?')) {
        e.preventDefault();
    }
});

updateRecommendation();
</script>
@endpush
@endsection
