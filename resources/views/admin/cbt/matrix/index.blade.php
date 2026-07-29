@extends('layouts.app')

@section('title', 'Matriks Kompetensi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Matriks Kompetensi</h3>
                <p class="text-subtitle text-muted">
                    Tampilan kompetensi karyawan berdasarkan skill
                </p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Matriks Kompetensi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="card-title">Skill Matrix - Karyawan vs Kompetensi</h4>
                    </div>
                    <div class="col-md-6">
                        <!-- Legend -->
                        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                            <span class="badge bg-secondary">Level 0 - Belum Terlatih</span>
                            <span class="badge bg-info">Level 1 - Novice</span>
                            <span class="badge bg-warning">Level 2 - Competent</span>
                            <span class="badge bg-primary">Level 3 - Proficient</span>
                            <span class="badge bg-success">Level 4 - Expert</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- Filter by Division --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <form method="GET" id="filterForm">
                            <label class="form-label fw-bold">Filter Divisi <span class="text-danger">*</span></label>
                            <select name="division_id" id="divisionFilter" class="form-select">
                                <option value="">-- Pilih Divisi --</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }}
                                        @if($division->department)
                                            ({{ $division->department->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="col-md-4">
                        @if($divisionId)
                            <div class="alert alert-info py-2 mb-0 mt-4">
                                <i class="bi bi-funnel me-1"></i>
                                Menampilkan skill khusus divisi ini
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-md-end d-flex flex-column flex-md-row gap-2 justify-content-md-end align-items-md-end mt-4">
                        <a href="{{ route('cbt.admin.employee-competencies.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-person-lines-fill me-1"></i> Level Skill Karyawan
                        </a>
                        <a href="{{ route('cbt.admin.division-skills.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-gear me-1"></i> Customisasi Kompetensi Divisi
                        </a>
                        @if($divisionId)
                            <a href="{{ route('cbt.admin.competency-history.print', ['division_id' => $divisionId]) }}"
                               class="btn btn-success" target="_blank">
                                <i class="bi bi-printer me-1"></i> Print Tabel
                            </a>
                        @else
                            <button type="button" class="btn btn-success" disabled title="Pilih divisi terlebih dahulu">
                                <i class="bi bi-printer me-1"></i> Print Tabel
                            </button>
                        @endif
                    </div>
                </div>

                @if(!$divisionId)
                    <div class="alert alert-info text-center py-5">
                        <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                        <h5>Pilih divisi untuk melihat matriks kompetensi</h5>
                        <p class="text-muted mb-0">Silakan pilih divisi dari dropdown di atas untuk menampilkan data kompetensi karyawan</p>
                    </div>
                @elseif($skills->isEmpty())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Belum ada kompetensi yang ditambahkan untuk divisi ini. Silakan tambahkan kompetensi terlebih dahulu di halaman <a href="{{ route('cbt.admin.division-skills.index') }}" class="alert-link">Kompetensi Divisi</a>.
                    </div>
                @elseif($employees->isEmpty())
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Tidak ada karyawan aktif yang ditemukan di divisi ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="competency-matrix-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="sticky-col bg-light" style="min-width: 50px;">#</th>
                                    <th class="sticky-col-2 bg-light" style="min-width: 200px;">Nama Inspector</th>
                                    @foreach($skills as $skill)
                                        <th class="text-center" style="min-width: 120px;">
                                            <small>{{ $skill->name }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $index => $employee)
                                    <tr>
                                        <td class="sticky-col bg-white">{{ $index + 1 }}</td>
                                        <td class="sticky-col-2 bg-white">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    @if($employee->photo)
                                                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="Avatar" class="rounded-circle">
                                                    @else
                                                        <span class="avatar-content bg-primary text-white">
                                                            {{ substr($employee->name, 0, 1) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $employee->name }}</strong>
                                                    <br><small class="text-muted">{{ $employee->nik }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($skills as $skill)
                                            @php
                                                $competency = null;
                                                $level = 0;
                                                
                                                if(isset($competencies[$employee->nik])) {
                                                    $competency = $competencies[$employee->nik]->firstWhere('skill_id', $skill->id);
                                                    $level = $competency ? $competency->level : 0;
                                                }
                                            @endphp
                                            <td class="text-center align-middle">
                                                @switch($level)
                                                    @case(0)
                                                        <span class="badge bg-secondary" title="Belum Terlatih">
                                                            Level 0
                                                        </span>
                                                        @break
                                                    @case(1)
                                                        <span class="badge bg-info" title="Novice">
                                                            Level 1
                                                        </span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge bg-warning" title="Competent">
                                                            Level 2
                                                        </span>
                                                        @break
                                                    @case(3)
                                                        <span class="badge bg-primary" title="Proficient">
                                                            Level 3
                                                        </span>
                                                        @break
                                                    @case(4)
                                                        <span class="badge bg-success" title="Expert">
                                                            Level 4
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary" title="Belum Terlatih">
                                                            Level 0
                                                        </span>
                                                @endswitch
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Ringkasan Statistik</h5>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-primary">
                                <div class="card-body py-3 text-center">
                                    <h3 class="mb-0 text-primary">{{ $employees->count() }}</h3>
                                    <small class="text-muted">Total Karyawan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-info">
                                <div class="card-body py-3 text-center">
                                    <h3 class="mb-0 text-info">{{ $skills->count() }}</h3>
                                    <small class="text-muted">Total Skill</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-success">
                                <div class="card-body py-3 text-center">
                                    @php
                                        $totalCompetencies = 0;
                                        foreach($competencies as $empCompetencies) {
                                            $totalCompetencies += $empCompetencies->count();
                                        }
                                    @endphp
                                    <h3 class="mb-0 text-success">{{ $totalCompetencies }}</h3>
                                    <small class="text-muted">Kompetensi Tercatat</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-warning">
                                <div class="card-body py-3 text-center">
                                    @php
                                        $expertCount = 0;
                                        foreach($competencies as $empCompetencies) {
                                            $expertCount += $empCompetencies->where('level', 4)->count();
                                        }
                                    @endphp
                                    <h3 class="mb-0 text-warning">{{ $expertCount }}</h3>
                                    <small class="text-muted">Expert (Level 4)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    /* Sticky columns for better horizontal scroll */
    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 1;
    }
    
    .sticky-col-2 {
        position: sticky;
        left: 50px;
        z-index: 1;
    }
    
    #competency-matrix-table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    #competency-matrix-table th,
    #competency-matrix-table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    
    #competency-matrix-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    
    #competency-matrix-table thead th.sticky-col,
    #competency-matrix-table thead th.sticky-col-2 {
        z-index: 3;
    }
    
    /* Avatar styles */
    .avatar {
        display: inline-flex;
        width: 32px;
        height: 32px;
    }
    
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-content {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 50%;
    }
    
    /* Badge styling */
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    
    /* Gap utility for legend */
    .gap-2 {
        gap: 0.5rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const divisionFilter = document.getElementById('divisionFilter');
    const filterForm = document.getElementById('filterForm');
    
    if (divisionFilter && filterForm) {
        divisionFilter.addEventListener('change', function() {
            window.autoSubmitForm(filterForm);
        });
    }
});
</script>
@endpush
