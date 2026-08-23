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
            <div class="card-header d-flex flex-column flex-lg-row flex-wrap gap-2 justify-content-between align-items-lg-center">
                <h4 class="card-title mb-2 mb-lg-0">Skill Matrix &mdash; Karyawan vs Kompetensi</h4>
                @if(!$divisionId || (!$skills->isEmpty() && !$employees->isEmpty()))
                    <div class="matrix-legend d-flex flex-wrap gap-2 justify-content-lg-end" role="group" aria-label="Filter berdasarkan level kompetensi">
                        <button type="button" class="legend-chip" data-level-filter="0" aria-pressed="false">
                            <span class="legend-dot dot-0"></span>L0 Belum Terlatih
                        </button>
                        <button type="button" class="legend-chip" data-level-filter="1" aria-pressed="false">
                            <span class="legend-dot dot-1"></span>L1 Novice
                        </button>
                        <button type="button" class="legend-chip" data-level-filter="2" aria-pressed="false">
                            <span class="legend-dot dot-2"></span>L2 Competent
                        </button>
                        <button type="button" class="legend-chip" data-level-filter="3" aria-pressed="false">
                            <span class="legend-dot dot-3"></span>L3 Proficient
                        </button>
                        <button type="button" class="legend-chip" data-level-filter="4" aria-pressed="false">
                            <span class="legend-dot dot-4"></span>L4 Expert
                        </button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-12 col-md-4">
                        <form method="GET" id="filterForm">
                            <label class="form-label fw-bold" for="divisionFilter">Filter Divisi</label>
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
                    <div class="col-12 col-md-8 d-flex flex-column flex-md-row flex-wrap gap-2 justify-content-md-end">
                        @if($divisionId)
                            <span class="badge bg-info-subtle text-info-emphasis align-self-center me-auto me-md-0 py-2 px-3">
                                <i class="bi bi-funnel me-1"></i>Skill khusus divisi ini
                            </span>
                        @endif
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

                @php
                    $levelLabels = [
                        0 => 'Belum Terlatih',
                        1 => 'Novice',
                        2 => 'Competent',
                        3 => 'Proficient',
                        4 => 'Expert',
                    ];
                    $bandClass = function ($v) {
                        if ($v >= 3.25) return 'band-4';
                        if ($v >= 2.25) return 'band-3';
                        if ($v >= 1.25) return 'band-2';
                        if ($v > 0) return 'band-1';
                        return 'band-0';
                    };
                @endphp

                @if(!$divisionId)
                    <div class="empty-state">
                        <i class="bi bi-diagram-3"></i>
                        <h5>Pilih divisi untuk melihat matriks kompetensi</h5>
                        <p class="text-muted mb-0">Silakan pilih divisi dari dropdown di atas untuk menampilkan data kompetensi karyawan</p>
                    </div>
                @elseif($skills->isEmpty())
                    <div class="empty-state warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <h5>Belum ada kompetensi untuk divisi ini</h5>
                        <p class="text-muted mb-0">Silakan tambahkan kompetensi terlebih dahulu di halaman <a href="{{ route('cbt.admin.division-skills.index') }}">Kompetensi Divisi</a>.</p>
                    </div>
                @elseif($employees->isEmpty())
                    <div class="empty-state warning">
                        <i class="bi bi-people"></i>
                        <h5>Tidak ada karyawan aktif</h5>
                        <p class="text-muted mb-0">Tidak ada karyawan dengan status aktif yang ditemukan di divisi ini.</p>
                    </div>
                @else
                    <div class="matrix-wrap table-responsive">
                        <table class="table matrix-table align-middle" id="competency-matrix-table">
                            <thead>
                                <tr>
                                    <th class="sticky-col col-num" scope="col">#</th>
                                    <th class="sticky-col-2 col-name" scope="col">Nama Inspector</th>
                                    @foreach($skills as $skill)
                                        <th class="col-skill text-center" scope="col" title="{{ $skill->name }}">
                                            <span>{{ $skill->name }}</span>
                                        </th>
                                    @endforeach
                                    <th class="col-avg text-center" scope="col" title="Rata-rata level seluruh skill">
                                        Avg
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $index => $employee)
                                    <tr>
                                        <td class="sticky-col col-num">{{ $index + 1 }}</td>
                                        <td class="sticky-col-2 col-name">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    @if($employee->photo)
                                                        <img src="{{ asset('storage/' . $employee->photo) }}" alt="{{ $employee->name }}" loading="lazy" class="rounded-circle">
                                                    @else
                                                        <span class="avatar-content">{{ substr($employee->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <strong class="d-block text-truncate">{{ $employee->name }}</strong>
                                                    <small class="text-muted">{{ $employee->nik }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($skills as $skill)
                                            @php
                                                $competency = isset($competencies[$employee->nik])
                                                    ? $competencies[$employee->nik]->firstWhere('skill_id', $skill->id)
                                                    : null;
                                                $level = $competency ? (int) $competency->level : 0;
                                                $tip = '<strong>' . e($skill->name) . '</strong><br>'
                                                    . e($employee->name)
                                                    . '<br>Level ' . $level . ' &mdash; ' . ($levelLabels[$level] ?? 'Belum Terlatih');
                                                if ($competency && $competency->updated_at) {
                                                    $tip .= '<br><small>Update: ' . $competency->updated_at->format('d M Y H:i') . '</small>';
                                                } else {
                                                    $tip .= '<br><small>Belum ada pencatatan</small>';
                                                }
                                            @endphp
                                            <td class="level-cell lvl-{{ $level }} text-center"
                                                data-level="{{ $level }}"
                                                tabindex="0"
                                                data-bs-toggle="tooltip"
                                                data-bs-html="true"
                                                data-bs-placement="top"
                                                title="{!! $tip !!}">
                                                <span class="lvl-num">{{ $level }}</span>
                                            </td>
                                        @endforeach
                                        @php
                                            $empAvg = $employeeAverages[$employee->nik] ?? 0;
                                        @endphp
                                        <td class="col-avg text-center">
                                            <div class="avg-value">{{ number_format($empAvg, 1) }}</div>
                                            <div class="avg-track" role="img" aria-label="Rata-rata level {{ number_format($empAvg, 1) }} dari 4">
                                                <div class="avg-fill {{ $bandClass($empAvg) }}" style="width: {{ min(100, $empAvg / 4 * 100) }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="sticky-col tfoot-label">Rata-rata Skill</th>
                                    @foreach($skills as $skill)
                                        @php $sAvg = $skillAverages[$skill->id] ?? 0; @endphp
                                        <td class="text-center">
                                            <span class="fw-semibold">{{ number_format($sAvg, 1) }}</span>
                                            <div class="avg-track mx-auto mt-1">
                                                <div class="avg-fill {{ $bandClass($sAvg) }}" style="width: {{ min(100, $sAvg / 4 * 100) }}%"></div>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        @php $overallAvg = $skillAverages->isNotEmpty() ? $skillAverages->avg() : 0; @endphp
                                        <span class="fw-bold">{{ number_format($overallAvg, 1) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <h5 class="mb-0">Ringkasan Statistik</h5>
                        </div>
                        @php
                            $totalCompetencies = 0;
                            $expertCount = 0;
                            foreach($competencies as $empCompetencies) {
                                $totalCompetencies += $empCompetencies->count();
                                $expertCount += $empCompetencies->where('level', 4)->count();
                            }
                        @endphp
                        <div class="col-6 col-md-3">
                            <div class="kpi-card">
                                <i class="bi bi-people kpi-icon kpi-primary"></i>
                                <div>
                                    <div class="kpi-value">{{ $employees->count() }}</div>
                                    <div class="kpi-label">Total Karyawan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card">
                                <i class="bi bi-lightning-charge kpi-icon kpi-info"></i>
                                <div>
                                    <div class="kpi-value">{{ $skills->count() }}</div>
                                    <div class="kpi-label">Total Skill</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card">
                                <i class="bi bi-journal-check kpi-icon kpi-success"></i>
                                <div>
                                    <div class="kpi-value">{{ $totalCompetencies }}</div>
                                    <div class="kpi-label">Kompetensi Tercatat</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card">
                                <i class="bi bi-patch-check kpi-icon kpi-warning"></i>
                                <div>
                                    <div class="kpi-value">{{ $expertCount }}</div>
                                    <div class="kpi-label">Expert (Level 4)</div>
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
    :root {
        --mx-cell-bg: #ffffff;
        --mx-head-bg: #f8f9fa;
        --mx-foot-bg: #f8f9fa;
        --mx-divider: rgba(0, 0, 0, .08);
        --lvl-0-bg: #e9ecef;   --lvl-0-fg: #495057;
        --lvl-1-bg: #cff4fc;   --lvl-1-fg: #055160;
        --lvl-2-bg: #fff3cd;   --lvl-2-fg: #664d03;
        --lvl-3-bg: #cfe2ff;   --lvl-3-fg: #084298;
        --lvl-4-bg: #a9dfbf;   --lvl-4-fg: #052e16;
    }

    html[data-bs-theme="dark"] {
        --mx-cell-bg: #212529;
        --mx-head-bg: #2b3035;
        --mx-foot-bg: #2b3035;
        --mx-divider: rgba(255, 255, 255, .12);
        --lvl-0-bg: #343a40;   --lvl-0-fg: #ced4da;
        --lvl-1-bg: #0e3a47;   --lvl-1-fg: #7ee1f5;
        --lvl-2-bg: #453208;   --lvl-2-fg: #ffda6a;
        --lvl-3-bg: #16295e;   --lvl-3-fg: #9ec1ff;
        --lvl-4-bg: #14522f;   --lvl-4-fg: #7ce3a1;
    }

    .matrix-table {
        --bs-table-bg: transparent;
        border-collapse: separate;
        border-spacing: 0;
    }

    .matrix-table th,
    .matrix-table td {
        vertical-align: middle;
    }

    .matrix-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: var(--mx-head-bg);
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: var(--bs-secondary-color);
        border-bottom: 2px solid var(--mx-divider);
    }

    .matrix-table .col-skill span {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.25;
        max-width: 120px;
        margin-inline: auto;
    }

    .matrix-table .col-num {
        width: 56px;
        min-width: 56px;
        max-width: 56px;
    }

    .matrix-table .col-name {
        min-width: 200px;
    }

    .matrix-table .col-skill {
        min-width: 76px;
    }

    .matrix-table .col-avg {
        min-width: 84px;
    }

    .matrix-table .sticky-col {
        position: sticky;
        left: 0;
        z-index: 1;
        background: var(--mx-cell-bg);
    }

    .matrix-table .sticky-col-2 {
        position: sticky;
        left: 56px;
        z-index: 1;
        background: var(--mx-cell-bg);
        border-right: 2px solid var(--mx-divider);
        box-shadow: inset -1px 0 0 var(--mx-divider);
    }

    .matrix-table thead th.sticky-col,
    .matrix-table thead th.sticky-col-2,
    .matrix-table tfoot .sticky-col {
        z-index: 3;
        background: var(--mx-head-bg);
    }

    .matrix-table tbody .sticky-col {
        background: var(--mx-cell-bg);
    }

    .matrix-table tfoot th,
    .matrix-table tfoot td {
        background: var(--mx-foot-bg);
        border-top: 2px solid var(--mx-divider);
        padding: .65rem .5rem;
    }

    .matrix-table .col-name .text-truncate {
        max-width: 150px;
    }

    .avatar {
        display: inline-flex;
        flex: 0 0 auto;
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
        color: #fff;
        background: var(--bs-primary);
        border-radius: 50%;
    }

    .level-cell {
        min-width: 76px;
        height: 52px;
        cursor: default;
        transition: opacity .2s ease, transform .15s ease;
    }

    .level-cell .lvl-num {
        font-size: 1.05rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .level-cell:focus-visible {
        outline: 2px solid var(--bs-primary);
        outline-offset: -2px;
    }

    .level-cell.dimmed {
        opacity: .14;
    }

    .lvl-0 { background: var(--lvl-0-bg); color: var(--lvl-0-fg); }
    .lvl-1 { background: var(--lvl-1-bg); color: var(--lvl-1-fg); }
    .lvl-2 { background: var(--lvl-2-bg); color: var(--lvl-2-fg); }
    .lvl-3 { background: var(--lvl-3-bg); color: var(--lvl-3-fg); }
    .lvl-4 { background: var(--lvl-4-bg); color: var(--lvl-4-fg); }

    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .28rem .7rem;
        font-size: .74rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        background: transparent;
        border: 1px solid var(--bs-border-color);
        border-radius: 2rem;
        cursor: pointer;
        transition: border-color .15s ease, background-color .15s ease, color .15s ease;
    }

    .legend-chip:hover {
        border-color: var(--bs-primary);
        color: var(--bs-body-color);
    }

    .legend-chip.active {
        background: var(--bs-primary);
        border-color: var(--bs-primary);
        color: #fff;
    }

    .legend-chip:focus-visible {
        outline: 2px solid var(--bs-primary);
        outline-offset: 2px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex: 0 0 auto;
    }

    .dot-0 { background: var(--lvl-0-bg); border: 1px solid var(--lvl-0-fg); }
    .dot-1 { background: var(--lvl-1-bg); border: 1px solid var(--lvl-1-fg); }
    .dot-2 { background: var(--lvl-2-bg); border: 1px solid var(--lvl-2-fg); }
    .dot-3 { background: var(--lvl-3-bg); border: 1px solid var(--lvl-3-fg); }
    .dot-4 { background: var(--lvl-4-bg); border: 1px solid var(--lvl-4-fg); }

    .legend-chip.active .legend-dot.dot-0,
    .legend-chip.active .legend-dot.dot-1,
    .legend-chip.active .legend-dot.dot-2,
    .legend-chip.active .legend-dot.dot-3,
    .legend-chip.active .legend-dot.dot-4 {
        background: #fff;
        border-color: currentColor;
    }

    .avg-value {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        line-height: 1.1;
    }

    .avg-track {
        width: 56px;
        height: 5px;
        margin-top: .35rem;
        overflow: hidden;
        background: var(--bs-border-color);
        border-radius: 99px;
    }

    .avg-fill {
        height: 100%;
        border-radius: 99px;
        transition: width .3s ease;
    }

    .band-0 { background: #adb5bd; }
    .band-1 { background: #0dcaf0; }
    .band-2 { background: #ffc107; }
    .band-3 { background: #0d6efd; }
    .band-4 { background: #198754; }

    .empty-state {
        padding: 3.5rem 1.5rem;
        text-align: center;
        background: var(--bs-tertiary-bg);
        border: 1px dashed var(--bs-border-color);
        border-radius: .75rem;
    }

    .empty-state i {
        display: block;
        margin-bottom: 1rem;
        font-size: 2.75rem;
        color: var(--bs-primary);
        opacity: .75;
    }

    .empty-state.warning i {
        color: var(--bs-warning);
    }

    .kpi-card {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: .85rem 1rem;
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: .6rem;
    }

    .kpi-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 42px;
        height: 42px;
        font-size: 1.25rem;
        border-radius: .5rem;
    }

    .kpi-primary { background: rgba(13, 110, 253, .12); color: #0d6efd; }
    .kpi-info    { background: rgba(13, 202, 240, .12); color: #0aa2c0; }
    .kpi-success { background: rgba(25, 135, 84, .12);  color: #198754; }
    .kpi-warning { background: rgba(255, 193, 7, .14);  color: #b58105; }

    html[data-bs-theme="dark"] .kpi-primary { color: #6ea8fe; }
    html[data-bs-theme="dark"] .kpi-info    { color: #6edcff; }
    html[data-bs-theme="dark"] .kpi-success { color: #75b798; }
    html[data-bs-theme="dark"] .kpi-warning { color: #ffda6a; }

    .kpi-value {
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .kpi-label {
        font-size: .78rem;
        color: var(--bs-secondary-color);
    }

    @media (prefers-reduced-motion: reduce) {
        .level-cell,
        .legend-chip,
        .avg-fill {
            transition: none;
        }
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

    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('#competency-matrix-table [data-bs-toggle="tooltip"]')
            .forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
    }

    const chips = document.querySelectorAll('.legend-chip');

    chips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            const wasActive = chip.getAttribute('aria-pressed') === 'true';
            chips.forEach(function(c) {
                c.setAttribute('aria-pressed', 'false');
                c.classList.remove('active');
            });

            const cells = document.querySelectorAll('#competency-matrix-table .level-cell');

            if (wasActive) {
                cells.forEach(function(cell) { cell.classList.remove('dimmed'); });
                return;
            }

            chip.setAttribute('aria-pressed', 'true');
            chip.classList.add('active');
            const level = chip.dataset.levelFilter;
            cells.forEach(function(cell) {
                cell.classList.toggle('dimmed', cell.dataset.level !== level);
            });
        });
    });
});
</script>
@endpush
