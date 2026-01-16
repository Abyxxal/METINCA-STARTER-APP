@extends('layouts.app')

@section('title', 'Skill per Divisi')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Skill per Divisi</h3>
                <p class="text-subtitle text-muted">Kelola skill yang berlaku untuk setiap divisi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Skill per Divisi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Pilih Divisi --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pilih Divisi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($divisions as $division)
                            <a href="{{ route('cbt.admin.division-skills.index', ['division_id' => $division->id]) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedDivisionId == $division->id ? 'active' : '' }}">
                                <div>
                                    <strong>{{ $division->name }}</strong>
                                    @if($division->department)
                                        <br><small class="{{ $selectedDivisionId == $division->id ? 'text-white-50' : 'text-muted' }}">{{ $division->department->name }}</small>
                                    @endif
                                </div>
                                @php
                                    $count = \App\Models\DivisionSkill::where('division_id', $division->id)->count();
                                @endphp
                                <span class="badge {{ $selectedDivisionId == $division->id ? 'bg-white text-primary' : 'bg-primary' }}">{{ $count }}</span>
                            </a>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                Tidak ada divisi
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Skill Divisi --}}
        <div class="col-md-8">
            @if($selectedDivision)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                <i class="bi bi-diagram-3 me-1"></i>
                                Skill untuk: {{ $selectedDivision->name }}
                            </h5>
                            @if($selectedDivision->department)
                                <small class="text-muted">{{ $selectedDivision->department->name }}</small>
                            @endif
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Skill
                        </button>
                    </div>
                    <div class="card-body">
                        @if($divisionSkills->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p>Belum ada skill untuk divisi ini</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Skill Pertama
                                </button>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Kode</th>
                                            <th>Nama Skill</th>
                                            <th class="text-center">Wajib</th>
                                            <th class="text-center" width="150">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($divisionSkills as $index => $ds)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><span class="badge bg-light-primary">{{ $ds->skill->code ?? '-' }}</span></td>
                                                <td>{{ $ds->skill->name ?? 'Skill tidak ditemukan' }}</td>
                                                <td class="text-center">
                                                    @if($ds->is_mandatory)
                                                        <span class="badge bg-success"><i class="bi bi-check"></i> Ya</span>
                                                    @else
                                                        <span class="badge bg-secondary">Tidak</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editModal{{ $ds->id }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $ds->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            {{-- Edit Modal --}}
                                            <div class="modal fade" id="editModal{{ $ds->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('cbt.admin.division-skills.update', $ds->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Skill: {{ $ds->skill->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Nama Skill <span class="text-danger">*</span></label>
                                                                    <input type="text" name="skill_name" class="form-control" value="{{ $ds->skill->name }}" required>
                                                                    <small class="text-muted">Ubah nama skill jika diperlukan</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" name="is_mandatory" value="1" id="mandatory{{ $ds->id }}" {{ $ds->is_mandatory ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="mandatory{{ $ds->id }}">Skill Wajib</label>
                                                                    </div>
                                                                    <small class="text-muted">Jika dicentang, skill ini wajib dimiliki semua karyawan di divisi ini</small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="bi bi-save me-1"></i> Simpan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Delete Modal --}}
                                            <div class="modal fade" id="deleteModal{{ $ds->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('cbt.admin.division-skills.destroy', $ds->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Hapus Skill</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Yakin ingin menghapus skill <strong>{{ $ds->skill->name }}</strong> dari divisi <strong>{{ $selectedDivision->name }}</strong>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Add Skill Modal --}}
                <div class="modal fade" id="addSkillModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('cbt.admin.division-skills.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="division_id" value="{{ $selectedDivisionId }}">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Skill untuk {{ $selectedDivision->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="form-label">Nama Skill <span class="text-danger">*</span></label>
                                        <input type="text" name="skill_name" class="form-control" placeholder="Contoh: CMM Operation, Balancing, dll" required>
                                        <small class="text-muted">Ketik nama skill baru yang ingin ditambahkan</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-arrow-left-circle fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">Pilih divisi di sebelah kiri untuk melihat dan mengelola skill</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
