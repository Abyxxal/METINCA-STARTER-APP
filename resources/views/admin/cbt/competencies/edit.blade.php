@extends('layouts.app')

@section('title', 'Edit Level Skill - ' . $employee->name)

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Level Skill Karyawan</h3>
                <p class="text-subtitle text-muted">{{ $employee->name }} ({{ $employee->nik }})</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.employee-competencies.index') }}">Level Skill</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Informasi Karyawan</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive">
<table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>NIK</strong></td>
                            <td>: {{ $employee->nik }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama</strong></td>
                            <td>: {{ $employee->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Divisi</strong></td>
                            <td>: {{ $employee->division->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jabatan</strong></td>
                            <td>: {{ $employee->position->name ?? '-' }}</td>
                        </tr>
                    
</div></table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h4 class="card-title">Level Skill</h4>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="small text-muted"><span id="bulkCount">0</span> skill dipilih</span>
                <select id="bulkAction" class="form-select form-select-sm w-auto">
                    <option value="">— Aksi massal —</option>
                    <option value="set_level">Set Level</option>
                    <option value="reset">Reset (hapus kompetensi)</option>
                </select>
                <select id="bulkLevel" class="form-select form-select-sm w-auto d-none">
                    @for($i = 0; $i <= 4; $i++)
                        <option value="{{ $i }}">Level {{ $i }} - {{ \App\Models\EmployeeCompetency::$levelLabels[$i] }}</option>
                    @endfor
                </select>
                <input type="text" id="bulkNotes" class="form-control form-control-sm w-auto" placeholder="Catatan opsional">
                <button type="button" id="btnBulkApply" class="btn btn-sm btn-primary" disabled>Terapkan</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" class="form-check-input" id="checkAll"></th>
                            <th>Skill</th>
                            <th>Level Saat Ini</th>
                            <th>Diverifikasi Oleh</th>
                            <th>Tanggal Verifikasi</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($divisionSkills as $skill)
                            @php
                                $competency = $existingCompetencies->get($skill->id);
                                $currentLevel = $competency ? $competency->level : 0;
                                $levelLabel = \App\Models\EmployeeCompetency::$levelLabels[$currentLevel] ?? 'None';
                            @endphp
                            <tr>
                                <td><input type="checkbox" class="form-check-input skill-check" data-id="{{ $skill->id }}"></td>
                                <td><strong>{{ $skill->name }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $currentLevel == 0 ? 'secondary' : 'primary' }}">
                                        Level {{ $currentLevel }} - {{ $levelLabel }}
                                    </span>
                                </td>
                                <td>{{ $competency->verifiedBy->name ?? '-' }}</td>
                                <td>{{ $competency?->verified_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td>
                                    <small>{{ $competency->notes ?? '-' }}</small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal{{ $skill->id }}">
                                        <i class="bi bi-pencil"></i> Edit Level
                                    </button>
                                    @if($competency)
                                        <form action="{{ route('cbt.admin.employee-competencies.destroy', [$employee, $skill->id]) }}" 
                                            method="POST" 
                                            class="d-inline form-reset-skill">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Reset
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal Edit Level -->
                            <div class="modal fade" id="editModal{{ $skill->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('cbt.admin.employee-competencies.update', $employee) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="skill_id" value="{{ $skill->id }}">
                                            
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Level - {{ $skill->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Level Baru <span class="text-danger">*</span></label>
                                                    <select name="level" class="form-select" required>
                                                        @for($i = 0; $i <= 4; $i++)
                                                            <option value="{{ $i }}" {{ $currentLevel == $i ? 'selected' : '' }}>
                                                                Level {{ $i }} - {{ \App\Models\EmployeeCompetency::$levelLabels[$i] }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan</label>
                                                    <textarea name="notes" class="form-control" rows="3" 
                                                        placeholder="Alasan perubahan level...">{{ $competency->notes ?? '' }}</textarea>
                                                </div>
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i>
                                                    <strong>Info:</strong> Perubahan ini akan tercatat dengan nama Anda sebagai verifikator.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada skill untuk divisi ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('cbt.admin.employee-competencies.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-reset-skill').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reset level skill?',
                text: 'Level skill ini akan direset ke 0.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal',
                confirmButtonColor: SWAL_BTN.danger,
                cancelButtonColor: SWAL_BTN.cancel
            }).then(function(result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // ============================================
    // BULK AKSI LEVEL SKILL
    // ============================================
    const checkAll   = document.getElementById('checkAll');
    const skillBoxes = document.querySelectorAll('.skill-check');
    const bulkCount  = document.getElementById('bulkCount');
    const bulkAction = document.getElementById('bulkAction');
    const bulkLevel  = document.getElementById('bulkLevel');
    const bulkNotes  = document.getElementById('bulkNotes');
    const btnBulkApply = document.getElementById('btnBulkApply');

    function refreshBulkBar() {
        const checked = document.querySelectorAll('.skill-check:checked');
        bulkCount.textContent = checked.length;
        btnBulkApply.disabled = checked.length === 0 || bulkAction.value === '';
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            skillBoxes.forEach(function (box) { box.checked = checkAll.checked; });
            refreshBulkBar();
        });
    }

    skillBoxes.forEach(function (box) {
        box.addEventListener('change', function () {
            const all = document.querySelectorAll('.skill-check').length;
            const checked = document.querySelectorAll('.skill-check:checked').length;
            checkAll.checked = checked === all && all > 0;
            refreshBulkBar();
        });
    });

    bulkAction.addEventListener('change', function () {
        bulkLevel.classList.toggle('d-none', this.value !== 'set_level');
        refreshBulkBar();
    });

    btnBulkApply.addEventListener('click', function () {
        const ids = Array.from(document.querySelectorAll('.skill-check:checked'))
            .map(function (box) { return box.dataset.id; });

        if (ids.length === 0) return;

        const isReset = bulkAction.value === 'reset';
        const summary = isReset
            ? ids.length + ' kompetensi akan DIHAPUS (kembali Level 0).'
            : ids.length + ' skill akan diubah ke Level ' + bulkLevel.value + '.';

        Swal.fire({
            title: 'Terapkan aksi massal?',
            text: summary,
            icon: isReset ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Terapkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: isReset ? SWAL_BTN.danger : SWAL_BTN.success,
            cancelButtonColor: SWAL_BTN.cancel
        }).then(function (result) {
            if (!result.isConfirmed) return;

            fetch('{{ route("cbt.admin.employee-competencies.bulk", $employee) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    skill_ids: ids,
                    action: bulkAction.value,
                    level: isReset ? null : (bulkLevel.value || null),
                    notes: bulkNotes.value.trim() || null
                })
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Berhasil' : 'Gagal',
                    text: data.message,
                    confirmButtonColor: '#5d87ff'
                }).then(function () { location.reload(); });
            })
            .catch(function (err) {
                Swal.fire({ icon: 'error', title: 'Error!', text: err.message || 'Terjadi kesalahan jaringan.' });
            });
        });
    });
});
</script>
@endpush
