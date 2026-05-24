@extends('layouts.app')

@section('title', 'Manajemen Departemen')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-building me-2"></i>Manajemen Departemen</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Departemen</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Daftar Departemen</h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahDept">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Departemen
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Departemen</th>
                                <th style="width: 120px;">Jumlah Divisi</th>
                                <th style="width: 120px;">Jumlah Karyawan</th>
                                <th style="width: 150px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $index => $dept)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $dept->name }}</strong></td>
                                <td class="text-center"><span class="badge bg-info">{{ $dept->divisions_count }}</span></td>
                                <td class="text-center"><span class="badge bg-success">{{ $dept->employees_count }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-warning" onclick="editDept({{ $dept->id }}, '{{ $dept->name }}')">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusDept({{ $dept->id }}, '{{ $dept->name }}')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox me-2"></i>Tidak ada data departemen
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- MODAL 1: TAMBAH DEPARTEMEN --}}
<div class="modal fade" id="modalTambahDept" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Departemen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="namaDeptTambah" class="form-label fw-bold">Nama Departemen <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="namaDeptTambah" placeholder="Masukkan nama departemen" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="simpanDept()">
                    <i class="bi bi-check-circle me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: EDIT DEPARTEMEN --}}
<div class="modal fade" id="modalEditDept" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Departemen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editIdDept" class="form-label">ID</label>
                    <input type="text" class="form-control" id="editIdDept" disabled>
                </div>
                <div class="mb-0">
                    <label for="editNamaDept" class="form-label fw-bold">Nama Departemen</label>
                    <input type="text" class="form-control" id="editNamaDept" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="updateDept()">
                    <i class="bi bi-check-circle me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 3: KONFIRMASI HAPUS --}}
<div class="modal fade" id="modalHapusDept" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin hapus departemen: <strong id="namaHapusDept"></strong> ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmHapusDept()">
                    <i class="bi bi-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Global functions untuk departemen
    window.simpanDept = function() {
        var nama = document.getElementById('namaDeptTambah').value.trim();
        
        if (!nama) {
            alert('Nama departemen harus diisi');
            return;
        }

        $.ajax({
            url: '/api/departments',
            type: 'POST',
            data: JSON.stringify({name: nama}),
            contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response) {
                alert('✅ Departemen berhasil disimpan');
                location.reload();
            },
            error: function(xhr) {
                alert('❌ Gagal menyimpan: ' + (xhr.responseJSON?.message || 'Error'));
            }
        });
    };

    window.editDept = function(id, nama) {
        document.getElementById('editIdDept').value = id;
        document.getElementById('editNamaDept').value = nama;
        var modal = new bootstrap.Modal(document.getElementById('modalEditDept'));
        modal.show();
    };

    window.updateDept = function() {
        var id = document.getElementById('editIdDept').value;
        var nama = document.getElementById('editNamaDept').value.trim();

        if (!nama) {
            alert('Nama departemen harus diisi');
            return;
        }

        $.ajax({
            url: '/api/departments/' + id,
            type: 'PUT',
            data: JSON.stringify({name: nama}),
            contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                alert('✅ Departemen berhasil diupdate');
                location.reload();
            },
            error: function(xhr) {
                alert('❌ Gagal update: ' + (xhr.responseJSON?.message || 'Error'));
            }
        });
    };

    window.hapusDept = function(id, nama) {
        document.getElementById('namaHapusDept').textContent = nama;
        window.deptIdHapus = id;
        var modal = new bootstrap.Modal(document.getElementById('modalHapusDept'));
        modal.show();
    };

    window.confirmHapusDept = function() {
        var id = window.deptIdHapus;
        $.ajax({
            url: '/api/departments/' + id,
            type: 'DELETE',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                alert('✅ Departemen berhasil dihapus');
                location.reload();
            },
            error: function(xhr) {
                alert('❌ Gagal hapus: ' + (xhr.responseJSON?.message || 'Error'));
            }
        });
    };

    // Reset form ketika modal ditutup
    document.getElementById('modalTambahDept').addEventListener('hidden.bs.modal', function() {
        document.getElementById('namaDeptTambah').value = '';
    });
</script>
@endpush
