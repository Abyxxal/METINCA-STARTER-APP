@extends('layouts.app')

@section('title', 'Tugaskan Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tugaskan Ujian</h3>
                <p class="text-subtitle text-muted">Pilih ujian dan karyawan yang akan mengikuti</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.sessions.index') }}">Sesi Ujian</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tugaskan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <form action="{{ route('cbt.admin.sessions.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pilih Ujian</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Ujian <span class="text-danger">*</span></label>
                            <select name="exam_id" id="examSelect" class="form-select @error('exam_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Ujian --</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" 
                                        data-skill="{{ $exam->skill->name ?? '-' }}"
                                        data-level="{{ $exam->target_level }}"
                                        data-kkm="{{ $exam->passing_score }}"
                                        data-duration="{{ $exam->duration_minutes }}"
                                        {{ old('exam_id', request('exam_id')) == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('exam_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="examInfo" class="alert alert-light d-none">
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Skill</small>
                                    <div class="fw-bold" id="examSkill">-</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Target Level</small>
                                    <div class="fw-bold" id="examLevel">-</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">KKM</small>
                                    <div class="fw-bold" id="examKkm">-</div>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Durasi</small>
                                    <div class="fw-bold" id="examDuration">- menit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pilih Karyawan</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Filter Divisi</label>
                                <select id="divisionFilter" class="form-select form-select-sm">
                                    <option value="">-- Semua Divisi --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Cari Karyawan</label>
                                <input type="text" id="searchEmployee" class="form-control form-control-sm" placeholder="Cari nama/NIK...">
                            </div>
                        </div>

                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Batal Semua</button>
                            <span class="text-muted small ms-2" id="filteredCount"></span>
                        </div>

                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover" id="employeeTable">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="15%">NIK</th>
                                        <th width="30%">Nama</th>
                                        <th width="25%">Divisi</th>
                                        <th width="25%">Posisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr class="employee-row" 
                                            data-name="{{ strtolower($employee->name) }}" 
                                            data-nik="{{ strtolower($employee->nik) }}"
                                            data-division="{{ $employee->division_id }}">
                                            <td>
                                                <input type="checkbox" name="employee_niks[]" value="{{ $employee->nik }}" class="employee-checkbox"
                                                    {{ in_array($employee->nik, old('employee_niks', [])) ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $employee->nik }}</td>
                                            <td>{{ $employee->name }}</td>
                                            <td>{{ $employee->division->name ?? '-' }}</td>
                                            <td>{{ $employee->position->name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada karyawan aktif</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @error('employee_niks')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
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
                                <td class="text-muted">Karyawan Dipilih</td>
                                <td class="fw-bold text-end" id="selectedCount">0</td>
                            </tr>
                        </table>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send"></i> Tugaskan Ujian
                            </button>
                            <a href="{{ route('cbt.admin.sessions.index') }}" class="btn btn-secondary">
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
    const examSelect = document.getElementById('examSelect');
    const examInfo = document.getElementById('examInfo');
    const searchInput = document.getElementById('searchEmployee');
    const divisionFilter = document.getElementById('divisionFilter');
    const employeeTable = document.getElementById('employeeTable');
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const filteredCountSpan = document.getElementById('filteredCount');

    // Show exam info when selected
    examSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (this.value) {
            examInfo.classList.remove('d-none');
            document.getElementById('examSkill').textContent = selected.dataset.skill;
            document.getElementById('examLevel').textContent = 'Level ' + selected.dataset.level;
            document.getElementById('examKkm').textContent = selected.dataset.kkm + '%';
            document.getElementById('examDuration').textContent = selected.dataset.duration + ' menit';
        } else {
            examInfo.classList.add('d-none');
        }
    });

    // Trigger change on page load if exam pre-selected
    if (examSelect.value) {
        examSelect.dispatchEvent(new Event('change'));
    }

    // Filter employees
    function filterEmployees() {
        const search = searchInput.value.toLowerCase();
        const divisionId = divisionFilter.value;
        let visibleCount = 0;

        document.querySelectorAll('.employee-row').forEach(row => {
            const name = row.dataset.name || '';
            const nik = row.dataset.nik || '';
            const division = row.dataset.division || '';

            const matchSearch = name.includes(search) || nik.includes(search);
            const matchDivision = !divisionId || division === divisionId;

            if (matchSearch && matchDivision) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        filteredCountSpan.textContent = `(${visibleCount} karyawan ditampilkan)`;
    }

    // Search employees
    searchInput.addEventListener('input', filterEmployees);
    divisionFilter.addEventListener('change', filterEmployees);

    // Initial filter
    filterEmployees();

    // Select/deselect all visible
    selectAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.employee-checkbox').forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = true;
            }
        });
        updateCount();
    });

    deselectAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = false);
        updateCount();
    });

    // Update count on checkbox change
    document.querySelectorAll('.employee-checkbox').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    function updateCount() {
        const count = document.querySelectorAll('.employee-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
    }

    updateCount();
});
</script>
@endpush
@endsection
