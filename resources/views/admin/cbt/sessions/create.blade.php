@extends('layouts.app')

@section('title', 'Tugaskan Ujian')

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tugaskan Ujian</h3>
                <p class="text-subtitle text-muted">Pilih soal dari Bank Soal dan tugaskan ke karyawan</p>
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
    <form action="{{ route('cbt.admin.sessions.store') }}" method="POST" id="formTugaskanUjian">
        @csrf
        
        <div class="row">
            {{-- LEFT COLUMN: Pilih Soal --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="bi bi-filter me-2"></i>Filter Soal</h4>
                    </div>
                    <div class="card-body">
                        {{-- Search Bar --}}
                        <div class="mb-3">
                            <label class="form-label">Cari Set Soal</label>
                            <input type="text" id="searchSetSoal" class="form-control" placeholder="Ketik nama set soal...">
                        </div>
                        
                        <div class="row g-3 mb-3">
                            {{-- Filter Divisi --}}
                            <div class="col-md-4">
                                <label class="form-label">Divisi</label>
                                <select id="filterDivisi" class="form-select">
                                    <option value="">-- Semua Divisi --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Filter Level --}}
                            <div class="col-md-4">
                                <label class="form-label">Level Kompetensi</label>
                                <select id="filterLevel" class="form-select">
                                    <option value="">-- Semua Level --</option>
                                    <option value="1">Level 1 - Novice</option>
                                    <option value="2">Level 2 - Competent</option>
                                    <option value="3">Level 3 - Proficient</option>
                                    <option value="4">Level 4 - Expert</option>
                                </select>
                            </div>
                            {{-- Filter Tipe Soal --}}
                            <div class="col-md-4">
                                <label class="form-label">Tipe Soal</label>
                                <select id="filterTipe" class="form-select">
                                    <option value="">-- Semua Tipe --</option>
                                    <option value="multiple_choice">Pilihan Ganda</option>
                                    <option value="essay">Essay</option>
                                    <option value="true_false">Benar/Salah</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Pilih Set Soal</h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllSoal">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllSoal">Batal Semua</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-sm table-hover" id="tableSoal">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th width="5%"></th>
                                        <th width="35%">Judul Set Soal</th>
                                        <th width="20%">Divisi</th>
                                        <th width="15%">Level</th>
                                        <th width="10%">Jumlah Soal</th>
                                        <th width="15%">Tipe</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodySoal">
                                    @forelse($questionSets as $set)
                                        <tr class="question-row" 
                                            data-division="{{ $set->skill->division_id ?? '' }}"
                                            data-level="{{ $set->for_level }}"
                                            data-type="{{ $set->type }}"
                                            data-set-title="{{ strtolower($set->set_title) }}">
                                            <td>
                                                <input type="checkbox" 
                                                       name="question_set_ids[]" 
                                                       value="{{ $set->question_set_id }}" 
                                                       data-total-questions="{{ $set->total_questions }}"
                                                       class="question-checkbox form-check-input">
                                            </td>
                                            <td>
                                                <strong>{{ $set->set_title }}</strong>
                                            </td>
                                            <td>
                                                <small>{{ $set->skill->division->name ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">Level {{ $set->for_level }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $set->total_questions }} soal</span>
                                            </td>
                                            <td>
                                                @if($set->type === 'pilihan_ganda')
                                                    <span class="badge bg-info">Pilihan Ganda</span>
                                                @elseif($set->type === 'esai')
                                                    <span class="badge bg-warning">Esai</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($set->type) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                                <p class="text-muted mt-2">Tidak ada set soal tersedia</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="peringatanTipe" class="alert alert-warning d-none" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Set soal dengan tipe berbeda (pilihan ganda / benar-salah dan essay) tidak bisa digabung dalam satu ujian. Hapus salah satu tipe set.
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0"><i class="bi bi-people me-2"></i>Pilih Karyawan</h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllKaryawan">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllKaryawan">Batal Semua</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Filter Divisi Karyawan</label>
                                <select id="filterDivisiKaryawan" class="form-select form-select-sm">
                                    <option value="">-- Semua Divisi --</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Cari Karyawan</label>
                                <input type="text" id="searchKaryawan" class="form-control form-control-sm" placeholder="Cari nama/NIK...">
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover" id="tableKaryawan">
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
                                                <input type="checkbox" name="employee_niks[]" value="{{ $employee->nik }}" class="employee-checkbox form-check-input">
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
                        <div class="mt-2">
                            <span class="text-muted small" id="karyawanInfo">{{ count($employees) }} karyawan aktif</span>
                        </div>
                        <div class="mt-1">
                            <span class="text-muted small" id="soalInfo"></span>
                        </div>
                        @error('employee_niks')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Konfigurasi & Ringkasan --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="bi bi-gear me-2"></i>Konfigurasi Ujian</h4>
                    </div>
                    <div class="card-body">
                        {{-- Durasi --}}
                        <div class="mb-3">
                            <label class="form-label">Durasi (menit) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                   value="{{ old('duration_minutes', 60) }}" min="5" max="300" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Passing Score --}}
                        <div class="mb-3">
                            <label class="form-label">Nilai Minimum Lulus (KKM) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror" 
                                       value="{{ old('passing_score', 70) }}" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('passing_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jadwal Mulai (Opsional) --}}
                        <div class="mb-3">
                            <label class="form-label">Jadwal Mulai Ujian</label>
                            <input type="datetime-local" name="scheduled_start_at" class="form-control @error('scheduled_start_at') is-invalid @enderror" 
                                   value="{{ old('scheduled_start_at') }}">
                            @error('scheduled_start_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jika diisi, karyawan tidak bisa membuka ujian sebelum waktu ini. Kosongkan jika bisa dikerjakan kapan saja.</small>
                        </div>

                        {{-- Tenggat Waktu --}}
                        <div class="mb-3">
                            <label class="form-label">Tenggat Waktu <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="deadline_at" class="form-control @error('deadline_at') is-invalid @enderror" 
                                   value="{{ old('deadline_at') }}" required>
                            @error('deadline_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Batas waktu karyawan harus menyelesaikan ujian</small>
                        </div>

                        <hr>

                        {{-- Ringkasan --}}
                        <h6 class="mb-3"><i class="bi bi-clipboard-data me-2"></i>Ringkasan</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Karyawan Dipilih</td>
                                <td class="fw-bold text-end" id="selectedKaryawanCount">0</td>
                            </tr>
                        </table>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
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
    // Elements
    const filterDivisi = document.getElementById('filterDivisi');
    const filterLevel = document.getElementById('filterLevel');
    const filterTipe = document.getElementById('filterTipe');
    const searchSetSoal = document.getElementById('searchSetSoal');
    
    const filterDivisiKaryawan = document.getElementById('filterDivisiKaryawan');
    const searchKaryawan = document.getElementById('searchKaryawan');
    
    // ============================================
    // FILTER SOAL
    // ============================================
    function filterSoal() {
        const divisi = filterDivisi.value;
        const level = filterLevel.value;
        const tipe = filterTipe.value;
        const searchText = searchSetSoal.value.toLowerCase().trim();
        let visibleCount = 0;

        document.querySelectorAll('.question-row').forEach(row => {
            const rowDivision = row.dataset.division || '';
            const rowLevel = row.dataset.level || '';
            const rowType = row.dataset.type || '';
            const rowSetTitle = row.dataset.setTitle || '';

            const matchDivisi = !divisi || rowDivision === divisi;
            const matchLevel = !level || rowLevel === level;
            const matchTipe = !tipe || rowType === tipe;
            const matchSearch = !searchText || rowSetTitle.includes(searchText);

            if (matchDivisi && matchLevel && matchTipe && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const soalInfoEl = document.getElementById('soalInfo');
        if (soalInfoEl) soalInfoEl.textContent = visibleCount + ' soal ditampilkan';
    }

    // Real-time filtering for all inputs
    searchSetSoal.addEventListener('input', filterSoal);
    filterDivisi.addEventListener('change', filterSoal);
    filterLevel.addEventListener('change', filterSoal);
    filterTipe.addEventListener('change', filterSoal);
    
    // Initialize filter on page load
    filterSoal();

    // Select/Deselect All Soal (visible only)
    const btnSelectAllSoal = document.getElementById('selectAllSoal');
    const btnDeselectAllSoal = document.getElementById('deselectAllSoal');
    
    if (btnSelectAllSoal) {
        btnSelectAllSoal.addEventListener('click', function() {
            document.querySelectorAll('.question-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const checkbox = row.querySelector('.question-checkbox');
                    if (checkbox) checkbox.checked = true;
                }
            });
            updateCounts();
            checkSetTypeWarning();
        });
    }

    if (btnDeselectAllSoal) {
        btnDeselectAllSoal.addEventListener('click', function() {
            document.querySelectorAll('.question-checkbox').forEach(cb => {
                cb.checked = false;
            });
            updateCounts();
            checkSetTypeWarning();
        });
    }

    // ============================================
    // WARNING SET TIPE CAMPURAN
    // ============================================
    function checkSetTypeWarning() {
        const types = new Set();
        document.querySelectorAll('.question-checkbox:checked').forEach(cb => {
            const row = cb.closest('tr');
            if (row && row.dataset.type) types.add(row.dataset.type);
        });
        const hasEssay = types.has('essay');
        const hasAuto = types.has('multiple_choice') || types.has('true_false');
        const warning = document.getElementById('peringatanTipe');
        if (warning) warning.classList.toggle('d-none', !(hasEssay && hasAuto));
    }

    // ============================================
    // FILTER KARYAWAN
    // ============================================
    function filterKaryawan() {
        const divisi = filterDivisiKaryawan.value;
        const search = searchKaryawan.value.toLowerCase().trim();
        let visibleCount = 0;

        document.querySelectorAll('.employee-row').forEach(row => {
            const rowDivision = row.dataset.division || '';
            const rowName = row.dataset.name || '';
            const rowNik = row.dataset.nik || '';

            const matchDivisi = !divisi || rowDivision === divisi;
            const matchSearch = !search || rowName.includes(search) || rowNik.includes(search);

            if (matchDivisi && matchSearch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('karyawanInfo').textContent = visibleCount + ' karyawan ditampilkan';
    }

    // Debounce utility
    function debounce(func, delay = 300) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    searchKaryawan.addEventListener('input', debounce(filterKaryawan, 150));
    filterDivisiKaryawan.addEventListener('change', filterKaryawan);

    // Initialize filter on page load
    filterKaryawan();

    // Select/Deselect All Karyawan (visible only)
    const btnSelectAllKaryawan = document.getElementById('selectAllKaryawan');
    const btnDeselectAllKaryawan = document.getElementById('deselectAllKaryawan');
    
    if (btnSelectAllKaryawan) {
        btnSelectAllKaryawan.addEventListener('click', function() {
            document.querySelectorAll('.employee-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const checkbox = row.querySelector('.employee-checkbox');
                    if (checkbox) checkbox.checked = true;
                }
            });
            updateCounts();
        });
    }

    if (btnDeselectAllKaryawan) {
        btnDeselectAllKaryawan.addEventListener('click', function() {
            document.querySelectorAll('.employee-checkbox').forEach(cb => {
                cb.checked = false;
            });
            updateCounts();
        });
    }

    // ============================================
    // UPDATE COUNTS
    // ============================================
    function updateCounts() {
        const karyawanCount = document.querySelectorAll('.employee-checkbox:checked').length;
        const karyawanCountElement = document.getElementById('selectedKaryawanCount');
        if (karyawanCountElement) {
            karyawanCountElement.textContent = karyawanCount;
        }
    }

    // Use event delegation for better performance and dynamic content
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('employee-checkbox') || 
            e.target.classList.contains('question-checkbox')) {
            updateCounts();
        }
        if (e.target.classList.contains('question-checkbox')) {
            checkSetTypeWarning();
        }
    });

    // Initial count on page load
    updateCounts();
    checkSetTypeWarning();

    // ============================================
    // FORM VALIDATION
    // ============================================
    document.getElementById('formTugaskanUjian').addEventListener('submit', function(e) {
        const soalCount = document.querySelectorAll('.question-checkbox:checked').length;
        const karyawanCount = document.querySelectorAll('.employee-checkbox:checked').length;
        
        if (soalCount === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 set soal!');
            return false;
        }
        
        if (karyawanCount === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 karyawan!');
            return false;
        }
        
        return true;
    });
});
</script>
@endpush
@endsection
