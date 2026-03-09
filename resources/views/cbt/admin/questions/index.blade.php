@extends('layouts.app')

@section('title', 'Bank Soal')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Bank Soal</h3>
                <p class="text-subtitle text-muted">Kelola soal-soal untuk ujian kompetensi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bank Soal</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Daftar Soal</h4>
            <a href="{{ route('cbt.admin.questions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Soal
            </a>
        </div>
        <div class="card-body">
            {{-- Filters --}}
            <form method="GET" class="row g-3 mb-4" id="filterForm">
                <div class="col-md-2">
                    <label class="form-label">Divisi</label>
                    <select name="division_id" id="division_id" class="form-select">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Skill</label>
                    <select name="skill_id" id="skill_id" class="form-select" {{ !request('division_id') ? 'disabled' : '' }}>
                        @if(request('division_id'))
                            <option value="">Semua Skill</option>
                            @foreach($skills as $skill)
                                <option value="{{ $skill->id }}" {{ request('skill_id') == $skill->id ? 'selected' : '' }}>
                                    {{ $skill->name }}
                                </option>
                            @endforeach
                        @else
                            <option value="">-- Pilih Divisi Terlebih Dahulu --</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Level</label>
                    <select name="for_level" class="form-select">
                        <option value="">Semua Level</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ request('for_level') == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                        <option value="true_false" {{ request('type') == 'true_false' ? 'selected' : '' }}>Benar/Salah</option>
                        <option value="essay" {{ request('type') == 'essay' ? 'selected' : '' }}>Essay</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('cbt.admin.questions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Judul Set Soal</th>
                            <th width="12%">Skill</th>
                            <th width="12%">Jabatan</th>
                            <th width="8%">Level</th>
                            <th width="10%">Jumlah Soal</th>
                            <th width="8%">Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questionSets as $i => $set)
                            <tr>
                                <td>{{ $questionSets->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $set->set_title }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $set->question_set_id }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $set->skill->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if(isset($set->targetPosition) && $set->targetPosition)
                                        <span class="badge bg-warning text-dark">{{ $set->targetPosition->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Semua Jabatan</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Level {{ $set->for_level }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $set->question_count }} soal</span>
                                </td>
                                <td>
                                    @if($set->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cbt.admin.questions.show-set', $set->question_set_id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail Set">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('cbt.admin.questions.edit-set', $set->question_set_id) }}" class="btn btn-sm btn-outline-warning" title="Edit Set">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('cbt.admin.questions.destroy-set', $set->question_set_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus set soal ini beserta {{ $set->question_count }} soal di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Set">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada soal. <a href="{{ route('cbt.admin.questions.create') }}">Buat soal pertama</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $questionSets->withQueryString()->links() }}
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const divisionSelect = document.getElementById('division_id');
    const skillSelect = document.getElementById('skill_id');
    const filterForm = document.getElementById('filterForm');
    
    divisionSelect.addEventListener('change', function() {
        const divisionId = this.value;
        
        if (divisionId) {
            // Fetch skills for selected division
            fetch(`/api/divisions/${divisionId}/skills`)
                .then(response => response.json())
                .then(data => {
                    // Reset skill dropdown
                    skillSelect.innerHTML = '<option value="">Semua Skill</option>';
                    
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
                    
                    // Auto submit form after loading skills (use global helper)
                    window.autoSubmitForm(filterForm);
                })
                .catch(error => console.error('Error fetching skills:', error));
        } else {
            // If no division selected, disable skill select and auto submit
            skillSelect.innerHTML = '<option value="">-- Pilih Divisi Terlebih Dahulu --</option>';
            skillSelect.disabled = true;
            skillSelect.value = '';
            window.autoSubmitForm(filterForm);
        }
    });
    
    // Auto submit when skill is selected
    skillSelect.addEventListener('change', function() {
        if (!skillSelect.disabled) {
            window.autoSubmitForm(filterForm);
        }
    });
    
    // Auto submit for other filters
    document.querySelectorAll('#filterForm select[name="for_level"], #filterForm select[name="type"], #filterForm select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            window.autoSubmitForm(filterForm);
        });
    });
});
</script>
@endpush
@endsection
