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
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h4 class="card-title">Level Skill</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
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
                                            class="d-inline"
                                            onsubmit="return confirm('Reset level skill ini ke 0?')">
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
                                <td colspan="6" class="text-center text-muted">Tidak ada skill untuk divisi ini</td>
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
