{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Master Data')

{{-- Untuk menggunakan css --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
    <style>
        /* Optimize table responsive behavior */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive table {
            width: 100%;
            min-width: 100%;
        }
        
        /* Make DataTable columns auto-fit content */
        table.dataTable thead th {
            padding: 10px 8px;
            font-size: 0.875rem;
        }
        
        table.dataTable tbody td {
            padding: 8px;
            font-size: 0.875rem;
        }
        
        /* Allow filter dropdowns to be more responsive */
        .form-select {
            min-width: 150px;
            max-width: 100%;
        }
        
        /* Tab content padding optimization */
        .tab-pane {
            padding: 0;
        }
        
        .tab-pane > div {
            width: 100%;
            overflow-x: auto;
        }

        /* Modal backdrop lebih gelap */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7);
            opacity: 1;
        }
    </style>
@endpush

{{-- Isi content --}}
@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    {{-- SECTION: Page Header --}}
                    {{-- Nama: Master Data --}}
                    {{-- Fungsi: Mengelola data referensi karyawan dan struktur organisasi (departemen) untuk mapping training requirements --}}
                    <h3>Master Data</h3>
                    <p class="text-subtitle text-muted">Kelola data karyawan dan departemen</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Master Data</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-body">
                    {{-- SECTION: Tab Navigation untuk Master Data --}}
                    {{-- Fungsi: Navigasi untuk mengelola 2 master data: Data Karyawan dan Departemen --}}
                    <!-- Nav tabs dengan styling distinction -->
                    <ul class="nav nav-tabs nav-justified border-bottom-2" id="masterDataTab" role="tablist" style="border-bottom: 3px solid #f0f0f0;">
                        {{-- TAB 1: Data Karyawan --}}
                        {{-- Isi: Daftar karyawan dengan NIK, nama, departemen, jabatan, shift, status (Active/Inactive) - support CRUD dan import Excel --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="karyawan-tab" data-bs-toggle="tab"
                                data-bs-target="#karyawan" type="button" role="tab" aria-controls="karyawan"
                                aria-selected="true" style="border-bottom: 3px solid transparent; padding-bottom: 12px;">
                                <i class="bi bi-people-fill me-2" style="color: #6366f1;"></i><span style="font-weight: 600; color: #1f2937;">Data Karyawan</span>
                            </button>
                        </li>
                        {{-- TAB 2: Data Departemen --}}
                        {{-- Isi: Daftar departemen dengan jumlah divisi dan karyawan --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="departemen-tab" data-bs-toggle="tab"
                                data-bs-target="#departemen" type="button" role="tab" aria-controls="departemen"
                                aria-selected="false" style="border-bottom: 3px solid transparent; padding-bottom: 12px;">
                                <i class="bi bi-building me-2" style="color: #8b5cf6;"></i><span style="font-weight: 600; color: #6b7280;">Departemen</span>
                            </button>
                        </li>
                    </ul>

                    <style>
                        .nav-link.active {
                            border-bottom: 3px solid #6366f1 !important;
                            color: #6366f1 !important;
                        }
                        .nav-link:hover {
                            background-color: #f9fafb;
                            border-radius: 4px 4px 0 0;
                        }
                    </style>

                    <!-- Tab panes -->
                    <div class="tab-content" id="masterDataTabContent">

                        {{-- ======================================================================== --}}
                        {{-- SECTION: TAB CONTENT 1 - DATA KARYAWAN --}}
                        {{-- Fungsi: Menampilkan dan mengelola profil semua karyawan dengan fitur CRUD --}}
                        {{-- Konten: Tabel karyawan, filter, pencarian, action buttons (Edit/Delete/Reset) --}}
                        {{-- ======================================================================== --}}
                        
                        <div class="tab-pane fade show active" id="karyawan" role="tabpanel"
                            aria-labelledby="karyawan-tab">
                            <div class="mt-4 px-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0) 100%); border-radius: 8px; padding: 20px;">
                                {{-- Header dengan tombol Tambah Karyawan --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="mb-1" style="color: #1f2937;"><i class="bi bi-people-fill me-2" style="color: #6366f1;"></i>Data Karyawan</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">Kelola informasi dan profil semua karyawan</p>
                                    </div>
                                    <button id="btnTambahKaryawan" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan" onclick="openTambahKaryawanModal()" style="background-color: #6366f1; border-color: #6366f1;">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Karyawan
                                    </button>
                                </div>

                                {{-- Filter Row: Filters + Reset + Search --}}
                                <div class="row g-2 mb-3 align-items-end">
                                    <div class="col-md-2">
                                        <label for="filterDepartemenKaryawan" class="form-label form-label-sm">Filter Departemen</label>
                                        <select id="filterDepartemenKaryawan" class="form-select form-select-sm">
                                            <option value="">Semua Dept</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="filterDivisiKaryawan" class="form-label form-label-sm">Filter Divisi</label>
                                        <select id="filterDivisiKaryawan" class="form-select form-select-sm">
                                            <option value="">Semua Divisi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="searchKaryawan" class="form-label form-label-sm">Cari Nama/NIK</label>
                                        <input type="text" class="form-control form-control-sm" id="searchKaryawan" placeholder="Ketik nama atau NIK...">
                                    </div>
                                    <div class="col-md-2 d-flex gap-1">
                                        <button id="btnResetFilter" class="btn btn-outline-secondary btn-sm" title="Reset semua filter">
                                            <i class="bi bi-arrow-counterclockwise"></i>Reset
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabel Data Karyawan --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover" id="tableKaryawan">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px; text-align: center;"><span class="text-muted">No</span></th>
                                                <th style="width: 90px; text-align: center;"><span class="text-muted">NIK</span></th>
                                                <th style="width: auto;"><span class="text-muted">Nama</span></th>
                                                <th style="width: 180px;"><span class="text-muted">Dept / Divisi</span></th>
                                                <th style="width: 80px; text-align: center;"><span class="text-muted">Status</span></th>
                                                <th style="width: 150px; text-align: center;"><span class="text-muted">Aksi</span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableKaryawanBody" style="background-color: white;">
                                            {{-- Data karyawan akan dimuat via AJAX dari API --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- ======================================================================== --}}
                        {{-- SECTION: TAB CONTENT 2 - DEPARTEMEN --}}
                        {{-- Fungsi: Menampilkan dan mengelola daftar departemen dengan divisi dan karyawan --}}
                        {{-- Konten: Tabel departemen, action buttons (Edit/Delete) --}}
                        {{-- ======================================================================== --}}

                        <div class="tab-pane fade" id="departemen" role="tabpanel" aria-labelledby="departemen-tab">
                            <div class="mt-4 px-3" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(139, 92, 246, 0) 100%); border-radius: 8px; padding: 20px;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="mb-1" style="color: #1f2937;"><i class="bi bi-building me-2" style="color: #8b5cf6;"></i>Daftar Departemen</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">Kelola struktur organisasi dan departemen</p>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="window.openTambahDeptModal()" style="background-color: #8b5cf6; border-color: #8b5cf6;">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Departemen
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table id="tableDepartemen" class="table table-sm table-striped table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Nama Departemen</th>
                                                <th style="width: 120px; text-align: center;">Jumlah Divisi</th>
                                                <th style="width: 120px; text-align: center;">Jumlah Karyawan</th>
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
                                                    <button type="button" class="btn btn-sm btn-warning me-1" onclick="editDept({{ $dept->id }}, '{{ $dept->name }}')" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusDept({{ $dept->id }}, '{{ $dept->name }}')" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Tidak ada data departemen</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Departemen Tab -->
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================== --}}
        {{-- SECTION: MODAL - DEPARTEMEN --}}
        {{-- Modal-modal untuk fitur CRUD Departemen (Tambah, Edit, Delete) --}}
        {{-- ======================================================================== --}}

        {{-- ======================================================================== --}}
        {{-- SECTION: MODAL - DEPARTEMEN --}}
        {{-- Modal-modal untuk fitur CRUD Departemen (Tambah, Edit, Delete) --}}
        {{-- ======================================================================== --}}

        {{-- MODAL 1: TAMBAH DEPARTEMEN (dengan nested divisions & positions) --}}
        <div class="modal fade" id="modalTambahDept" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Departemen</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formTambahDept" onsubmit="return false;">
                            {{-- Nama Departemen --}}
                            <div class="mb-4">
                                <label for="namaDeptTambah" class="form-label fw-bold">Nama Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namaDeptTambah" placeholder="Masukkan nama departemen" required>
                            </div>

                            <hr>

                            {{-- Divisi Section dengan Nested Positions --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-diagram-3 me-1"></i>Divisi & Jabatan <span class="text-danger">*</span>
                                </label>
                                
                                <div id="containerDivisiTambah" class="mb-3">
                                    <!-- Divisi dengan nested positions akan ditambah di sini -->
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.tambahFieldDivisiTambah()">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Divisi
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="window.simpanDept()">
                            <i class="bi bi-check-circle me-1"></i>Simpan Departemen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL 2: EDIT DEPARTEMEN --}}
        <div class="modal fade" id="modalEditDept" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formEditDept">
                            <input type="hidden" id="editIdDept">
                            
                            {{-- Nama Departemen --}}
                            <div class="mb-4">
                                <label for="editNamaDept" class="form-label fw-bold">Nama Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editNamaDept" placeholder="Masukkan nama departemen" required>
                            </div>

                            <hr>

                            {{-- Divisi & Jabatan Section dengan Nested Positions --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-diagram-3 me-1"></i>Divisi & Jabatan <span class="text-danger">*</span>
                                </label>
                                
                                <div id="containerDivisiEdit" class="mb-3">
                                    <!-- Divisi dengan nested positions akan ditampilkan di sini -->
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.tambahFieldDivisiEdit()">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Divisi
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" onclick="window.updateDept()">
                            <i class="bi bi-check-circle me-1"></i>Update Departemen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL 3: KONFIRMASI HAPUS DEPARTEMEN --}}
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

        {{-- ======================================================================== --}}
        {{-- SECTION: MODAL - DATA KARYAWAN --}}
        {{-- Modal-modal untuk fitur CRUD Data Karyawan (Tambah, Edit, Delete) --}}
        {{-- ======================================================================== --}}

        {{-- Modal Tambah Karyawan --}}
        <div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-labelledby="modalTambahKaryawanLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTambahKaryawanLabel">
                            <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahKaryawan">
                            {{-- Two Column Layout --}}
                            <div class="row">
                                {{-- LEFT COLUMN: Account & Personal Info --}}
                                <div class="col-md-6">
                                    {{-- NIK --}}
                                    <div class="mb-3">
                                        <label for="idKaryawan" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="idKaryawan" placeholder="Contoh: E001" required>
                                    </div>

                                    {{-- Full Name --}}
                                    <div class="mb-3">
                                        <label for="namaKaryawan" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="namaKaryawan" placeholder="Masukkan nama lengkap" required>
                                    </div>

                                    {{-- Email Address --}}
                                    <div class="mb-3">
                                        <label for="emailKaryawan" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="emailKaryawan" placeholder="nama@company.com" required>
                                    </div>

                                    {{-- Default Password --}}
                                    <div class="mb-3">
                                        <label for="passwordKaryawan" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="passwordKaryawan" placeholder="Masukkan password (minimal 6 karakter)" required>
                                    </div>
                                </div>

                                {{-- RIGHT COLUMN: Employment Data --}}
                                <div class="col-md-6">
                                    {{-- Department --}}
                                    <div class="mb-3">
                                        <label for="departemenKaryawan" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                        <select class="form-select" id="departemenKaryawan" required>
                                            <option value="">-- Pilih Departemen --</option>
                                        </select>
                                    </div>

                                    {{-- Division --}}
                                    <div class="mb-3">
                                        <label for="divisiKaryawan" class="form-label fw-bold">Division <span class="text-danger">*</span></label>
                                        <select class="form-select" id="divisiKaryawan" required>
                                            <option value="">-- Pilih Divisi --</option>
                                        </select>
                                    </div>

                                    {{-- Position --}}
                                    <div class="mb-3">
                                        <label for="jabatanKaryawan" class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                                        <select class="form-select" id="jabatanKaryawan" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                        </select>
                                    </div>

                                    {{-- Join Date --}}
                                    <div class="mb-3">
                                        <label for="joinDateKaryawan" class="form-label fw-bold">Join Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="joinDateKaryawan" required>
                                    </div>

                                    {{-- Status --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="statusKaryawan" id="statusAktif" value="Aktif" checked>
                                                <label class="form-check-label" for="statusAktif">
                                                    Aktif
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="statusKaryawan" id="statusNonAktif" value="Non-Aktif">
                                                <label class="form-check-label" for="statusNonAktif">
                                                    Non-Aktif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fotoKaryawan" class="form-label fw-bold">Upload Photo</label>
                                        <input type="file" class="form-control" id="fotoKaryawan" accept="image/*">
                                        <small class="text-muted d-block mt-1">Format: JPG, PNG (Max: 2MB)</small>
                                        <div id="previewFoto" class="mt-2">
                                            <img id="imageFotoPreview" src="" alt="Preview" style="max-width: 150px; max-height: 150px; display: none; border-radius: 5px; border: 1px solid #dee2e6;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End of Two Column Layout --}}
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSimpanKaryawan">
                            <i class="bi bi-check-circle me-2"></i>Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Edit Karyawan --}}
        <div class="modal fade" id="modalEditKaryawan" tabindex="-1" aria-labelledby="modalEditKaryawanLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalEditKaryawanLabel">
                            <i class="bi bi-pencil-square me-2"></i>Edit Data Karyawan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditKaryawan">
                            {{-- Hidden field untuk store NIK lama --}}
                            <input type="hidden" id="editKaryawanId" value="">
                            
                            {{-- Two Column Layout --}}
                            <div class="row">
                                {{-- LEFT COLUMN: Account & Personal Info --}}
                                <div class="col-md-6">
                                    {{-- NIK --}}
                                    <div class="mb-3">
                                        <label for="editIdKaryawan" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editIdKaryawan" placeholder="Contoh: E001" required>
                                    </div>

                                    {{-- Full Name --}}
                                    <div class="mb-3">
                                        <label for="editNamaKaryawan" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNamaKaryawan" placeholder="Masukkan nama lengkap" required>
                                    </div>

                                    {{-- Email Address --}}
                                    <div class="mb-3">
                                        <label for="editEmailKaryawan" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="editEmailKaryawan" placeholder="nama@company.com" required>
                                    </div>

                                    {{-- Password --}}
                                    <div class="mb-3">
                                        <label for="editPasswordKaryawan" class="form-label fw-bold">Password <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                                        <input type="password" class="form-control" id="editPasswordKaryawan" placeholder="Masukkan password baru (opsional)">
                                    </div>
                                </div>

                                {{-- RIGHT COLUMN: Employment Data & Photo --}}
                                <div class="col-md-6">
                                    {{-- Department --}}
                                    <div class="mb-3">
                                        <label for="editDepartemenKaryawan" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editDepartemenKaryawan" required>
                                            <option value="">-- Pilih Departemen --</option>
                                        </select>
                                    </div>

                                    {{-- Division --}}
                                    <div class="mb-3">
                                        <label for="editDivisiKaryawan" class="form-label fw-bold">Division <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editDivisiKaryawan" required>
                                            <option value="">-- Pilih Divisi --</option>
                                        </select>
                                    </div>

                                    {{-- Position --}}
                                    <div class="mb-3">
                                        <label for="editJabatanKaryawan" class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editJabatanKaryawan" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                        </select>
                                    </div>

                                    {{-- Status --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusAktif" value="Aktif" checked>
                                                <label class="form-check-label" for="editStatusAktif">
                                                    Aktif
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusNonAktif" value="Non-Aktif">
                                                <label class="form-check-label" for="editStatusNonAktif">
                                                    Non-Aktif
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Upload Photo --}}
                                    <div class="mb-3">
                                        <label for="editFotoKaryawan" class="form-label fw-bold">Upload Photo</label>
                                        <input type="file" class="form-control" id="editFotoKaryawan" accept="image/*">
                                        <small class="text-muted d-block mt-1">Format: JPG, PNG (Max: 2MB)</small>
                                        <div id="editPreviewFoto" class="mt-2">
                                            <img id="editImageFotoLama" src="" alt="Foto Lama" style="max-width: 150px; max-height: 150px; display: none; border-radius: 5px; border: 1px solid #dee2e6;">
                                            <img id="editImageFotoPreview" src="" alt="Preview Baru" style="max-width: 150px; max-height: 150px; display: none; border-radius: 5px; border: 1px solid #dee2e6; margin-left: 10px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End of Two Column Layout --}}
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="button" class="btn btn-info text-white" id="btnUpdateKaryawan">
                            <i class="bi bi-check-circle me-2"></i>Perbarui Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Hapus Karyawan --}}
        <div class="modal fade" id="modalHapusKaryawan" tabindex="-1" aria-labelledby="modalHapusKaryawanLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalHapusKaryawanLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Karyawan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hapusKaryawanId">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Perhatian!</strong> Data karyawan yang dihapus tidak dapat dikembalikan.
                        </div>
                        <p class="mb-2">Anda yakin ingin menghapus karyawan berikut?</p>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img id="hapusFotoKaryawan" src="" alt="Foto" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0" id="hapusNamaKaryawan"></h6>
                                        <small class="text-muted" id="hapusNikKaryawan"></small>
                                    </div>
                                </div>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="100"><strong>Departemen:</strong></td>
                                        <td id="hapusDepartemenKaryawan"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jabatan:</strong></td>
                                        <td id="hapusJabatanKaryawan"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td id="hapusStatusKaryawan"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <p class="text-danger mt-3 mb-0">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Data ini akan <strong>dihapus secara permanen</strong> dari sistem.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="btnKonfirmasiHapusKaryawan">
                            <i class="bi bi-trash me-1"></i>Ya, Hapus Karyawan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Untuk menggunakan js --}}
@push('scripts')
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global error handler
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('❌ GLOBAL ERROR:', msg, 'at', url, ':', lineNo);
            return false;
        };

        // ===== MODAL TAMBAH DEPARTEMEN - OPEN FUNCTION =====
        // Fungsi ini SELALU membuat instance modal baru untuk menghindari state korup
        window.openTambahDeptModal = function() {
            console.log('🔘 openTambahDeptModal() called');
            
            var modalEl = document.getElementById('modalTambahDept');
            if (!modalEl) {
                console.error('❌ Modal element #modalTambahDept not found!');
                return;
            }
            
            // 1. Pastikan tidak ada backdrop yang tersisa dari sebelumnya
            document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                el.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // 2. Dispose instance lama jika ada
            var existingInstance = bootstrap.Modal.getInstance(modalEl);
            if (existingInstance) {
                console.log('🗑️ Disposing existing modal instance');
                existingInstance.dispose();
            }
            
            // 3. Reset form dan clear container SEBELUM modal dibuka
            var form = document.getElementById('formTambahDept');
            if (form) {
                form.reset();
                console.log('✓ Form reset');
            }
            
            var divContainer = document.getElementById('containerDivisiTambah');
            if (divContainer) {
                divContainer.innerHTML = '';
                console.log('✓ Divisi container cleared');
            }
            
            // 4. Tambah 1 divisi field awal
            if (window.tambahFieldDivisiTambah) {
                window.tambahFieldDivisiTambah();
                console.log('✓ Initial divisi field added');
            }
            
            // 5. Buat instance BARU dan tampilkan modal
            console.log('🆕 Creating new modal instance');
            var modal = new bootstrap.Modal(modalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            modal.show();
            console.log('✅ Modal opened successfully');
        };

        // ===== DEPARTEMEN FUNCTIONS =====
        // ===== DEPARTEMEN FUNCTIONS - Nested Divisions & Positions =====
        
        // Tambah division dengan nested positions
        window.tambahFieldDivisiTambah = function() {
            try {
                var container = document.getElementById('containerDivisiTambah');
                
                if (!container) {
                    console.error('❌ containerDivisiTambah not found!');
                    return false;
                }
                
                var divIndex = container.querySelectorAll('.divisi-wrapper').length;
                console.log('📍 Adding divisi field with index:', divIndex);
                
                var html = `
                    <div class="mb-3 p-3 border-2 border-primary rounded bg-light divisi-wrapper" data-div-index="${divIndex}">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-1">
                                <span class="badge bg-primary">Divisi ${divIndex + 1}</span>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control form-control-sm divisi-name-input" placeholder="Nama divisi (contoh: IT Support)" required>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.divisi-wrapper').remove()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Nested Positions Container -->
                        <div class="ms-3 mb-2 p-3 bg-white rounded border">
                            <label class="form-label small fw-bold mb-2">
                                <i class="bi bi-briefcase me-1"></i>Jabatan untuk divisi ini:
                            </label>
                            <div class="positions-container" data-div-index="${divIndex}">
                                <!-- Positions akan ditambah di sini -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="window.tambahFieldPositionTambah(${divIndex})">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                            </button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                console.log('✅ Divisi field added successfully');
                return true;
            } catch(error) {
                console.error('❌ Error in tambahFieldDivisiTambah:', error.message);
                console.error('Stack:', error.stack);
                return false;
            }
        };

        // Load dan render departemen table via AJAX
        window.loadDepartemenTable = function() {
            console.log('📡 Loading departemen table...');
            
            $.ajax({
                url: '/api/departments',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('✅ Departments loaded:', response.data);
                    
                    if (response.success && response.data) {
                        var tbody = document.querySelector('#tableDepartemen tbody');
                        
                        if (!tbody) {
                            console.error('❌ Table body not found');
                            return;
                        }
                        
                        // Clear existing rows
                        tbody.innerHTML = '';
                        
                        if (response.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data departemen</td></tr>';
                            return;
                        }
                        
                        // Render rows
                        response.data.forEach((dept, index) => {
                            var row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><strong>${dept.name}</strong></td>
                                    <td class="text-center"><span class="badge bg-info">${dept.divisions_count || 0}</span></td>
                                    <td class="text-center"><span class="badge bg-success">${dept.employees_count || 0}</span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning me-1" onclick="window.editDept(${dept.id}, '${dept.name}')" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="window.hapusDept(${dept.id}, '${dept.name}')" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                        
                        console.log('✅ Table rendered with', response.data.length, 'departments');
                    }
                },
                error: function(xhr) {
                    console.error('❌ Error loading departments:', xhr);
                    var tbody = document.querySelector('#tableDepartemen tbody');
                    if (tbody) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
                    }
                }
            });
        };

        // Load both Karyawan and Departemen tables sekaligus
        window.loadBothTables = function() {
            console.log('🔄 Loading both tables (Karyawan & Departemen)...');
            window.loadKaryawanTable();  // Now global scope
            window.loadDepartemenTable();
        };

        // Tambah position untuk division tertentu
        window.tambahFieldPositionTambah = function(divIndex) {
            try {
                var container = document.querySelector(`.positions-container[data-div-index="${divIndex}"]`);
                
                if (!container) {
                    console.error('❌ positions-container with data-div-index="' + divIndex + '" not found');
                    return false;
                }
                
                var posIndex = container.querySelectorAll('.position-field').length;
                console.log('📍 Adding position field - divIndex:', divIndex, 'posIndex:', posIndex);
                
                var html = `
                    <div class="mb-2 p-2 bg-light border rounded position-field" data-pos-index="${posIndex}">
                        <div class="row align-items-center">
                            <div class="col-md-10">
                                <input type="text" class="form-control form-control-sm position-name-input" placeholder="Nama jabatan (contoh: Senior Support)" required>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.position-field').remove()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                console.log('✅ Position field added successfully');
                return true;
            } catch(error) {
                console.error('❌ Error in tambahFieldPositionTambah:', error.message);
                console.error('Stack:', error.stack);
                return false;
            }
        };

        // Simpan department dengan semua divisions & positions
        window.simpanDept = function() {
            var nama = document.getElementById('namaDeptTambah').value.trim();
            var divisiWrappers = document.querySelectorAll('#containerDivisiTambah .divisi-wrapper');

            if (!nama) {
                Swal.fire({title: 'Validasi!', text: 'Nama departemen harus diisi', icon: 'warning'});
                return;
            }

            if (divisiWrappers.length === 0) {
                Swal.fire({title: 'Validasi!', text: 'Minimal 1 divisi harus diisi', icon: 'warning'});
                return;
            }

            // Collect dan validate data
            var divisiData = [];
            var isValid = true;

            divisiWrappers.forEach(function(divWrapper, divIndex) {
                var divName = divWrapper.querySelector('.divisi-name-input').value.trim();

                if (!divName) {
                    Swal.fire({title: 'Validasi!', text: 'Nama divisi harus diisi', icon: 'warning'});
                    isValid = false;
                    return;
                }

                var positionFields = divWrapper.querySelectorAll('.position-field .position-name-input');
                var positions = [];

                positionFields.forEach(function(posInput) {
                    var posName = posInput.value.trim();
                    if (posName) {
                        positions.push(posName);
                    }
                });

                if (positions.length === 0) {
                    Swal.fire({
                        title: 'Validasi!',
                        text: 'Divisi "' + divName + '" harus memiliki minimal 1 jabatan',
                        icon: 'warning'
                    });
                    isValid = false;
                    return;
                }

                divisiData.push({
                    name: divName,
                    positions: positions
                });
            });

            if (!isValid || divisiData.length === 0) return;

            // Log data yang akan dikirim
            var payloadData = {name: nama};
            console.log('📦 Sending payload:', JSON.stringify(payloadData));
            console.log('✅ Departemen nama:', nama);
            console.log('📋 Divisi data:', divisiData);

            // Buat departemen
            $.ajax({
                url: '/api/departments',
                type: 'POST',
                data: JSON.stringify(payloadData),
                contentType: 'application/json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.data && response.data.id) {
                        var deptId = response.data.id;
                        
                        // Create divisions & positions
                        var allPromises = [];

                        divisiData.forEach(function(divData) {
                            var divPromise = $.ajax({
                                url: '/api/divisions',
                                type: 'POST',
                                data: JSON.stringify({name: divData.name, department_id: deptId}),
                                contentType: 'application/json',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                            });

                            allPromises.push(
                                divPromise.then(function(divResponse) {
                                    if (divResponse.data && divResponse.data.id) {
                                        var divId = divResponse.data.id;
                                        var posPromises = [];
                                        divData.positions.forEach(function(posName) {
                                            posPromises.push(
                                                $.ajax({
                                                    url: '/api/positions',
                                                    type: 'POST',
                                                    data: JSON.stringify({name: posName, division_id: divId}),
                                                    contentType: 'application/json',
                                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                                })
                                            );
                                        });
                                        return $.when.apply($, posPromises);
                                    }
                                })
                            );
                        });

                        // Tunggu semua selesai
                        $.when.apply($, allPromises).done(function() {
                            console.log('✅ All AJAX operations completed successfully');
                            
                            // 1. Tutup Modal dengan DISPOSE untuk membersihkan instance
                            var modalEl = document.getElementById('modalTambahDept');
                            var modalInstance = bootstrap.Modal.getInstance(modalEl);
                            
                            if (modalInstance) {
                                console.log('🔄 Disposing modal instance...');
                                // Hide dan dispose modal instance agar tidak ada state korup
                                modalInstance.hide();
                                
                                // Tunggu transisi selesai sebelum dispose
                                modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                                    modalEl.removeEventListener('hidden.bs.modal', onHidden);
                                    modalInstance.dispose();
                                    console.log('✅ Modal instance disposed');
                                }, { once: true });
                            } else {
                                console.log('⚠️ No modal instance found, just hiding element');
                                modalEl.classList.remove('show');
                                modalEl.style.display = 'none';
                            }
                            
                            // 2. Cleanup SETELAH modal transition selesai (300ms adalah default Bootstrap transition)
                            setTimeout(function() {
                                // Force cleanup jika masih ada sisa backdrop
                                document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                                    el.remove();
                                });
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';
                                console.log('✅ Backdrop cleanup completed');
                            }, 350);

                            // 3. Reload tabel
                            window.loadBothTables();

                            // 4. Tampilkan notifikasi sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Departemen, divisi, dan jabatan berhasil disimpan',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                timer: 1500,
                                timerProgressBar: true
                            });

                        }).fail(function(xhr) {
                            // Handle error simpan detail
                            console.error('❌ Error details:', xhr);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menyimpan detail data',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }).always(function() {
                            // Reset tombol (jika pakai loading)
                            // btnSimpan.disabled = false;
                            // btnSimpan.innerHTML = originalText;
                        });
                    }
                },
                error: function(xhr) {
                    console.error('❌ Error creating department:', xhr);
                    console.error('Status:', xhr.status, xhr.statusText);
                    console.error('Response:', xhr.responseText);
                    
                    var errorMsg = 'Gagal membuat departemen';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                        if (response.errors) {
                            console.error('Validation errors:', response.errors);
                            errorMsg += '\n' + JSON.stringify(response.errors);
                        }
                    } catch(e) {
                        console.error('Could not parse error response');
                    }
                    
                    Swal.fire({
                        title: 'Gagal!',
                        text: errorMsg,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                    // btnSimpan.disabled = false;
                    // btnSimpan.innerHTML = originalText;
                }
            });
        };

        {{-- ======================================================================== --}}
        {{-- SECTION: JAVASCRIPT - DEPARTEMEN FUNCTIONS --}}
        {{-- Functions untuk CRUD Departemen: editDept, updateDept, hapusDept, dll --}}
        {{-- ======================================================================== --}}

        // Load dan buka modal edit dengan nested structure
        window.editDept = function(id, nama) {
            console.log('🔧 Edit department:', id, nama);
            
            var deptId = id;
            document.getElementById('editIdDept').value = deptId;
            document.getElementById('editNamaDept').value = nama;
            document.getElementById('containerDivisiEdit').innerHTML = '';
            
            // Load divisions & positions
            $.ajax({
                url: '/api/divisions?department_id=' + deptId,
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    console.log('✅ Divisions loaded:', response.data);
                    
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(division) {
                            window.addExistingDivisionEdit(division.id, division.name);
                        });
                    }
                    
                    // Open modal after loading
                    var modal = new bootstrap.Modal(document.getElementById('modalEditDept'));
                    modal.show();
                },
                error: function(xhr) {
                    console.error('❌ Error loading divisions:', xhr);
                    var modal = new bootstrap.Modal(document.getElementById('modalEditDept'));
                    modal.show();
                }
            });
        };

        // Add existing division ke container (dengan nested positions)
        window.addExistingDivisionEdit = function(divId, divName) {
            var container = document.getElementById('containerDivisiEdit');
            
            var html = `
                <div class="mb-3 p-3 border-2 border-primary rounded bg-light divisi-wrapper-edit" data-div-id="${divId}">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm divisi-name-edit" value="${divName}" data-original="${divName}" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.divisi-wrapper-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Nested Positions Container -->
                    <div class="ms-3 mb-2 p-3 bg-white rounded border">
                        <label class="form-label small fw-bold mb-2">
                            <i class="bi bi-briefcase me-1"></i>Jabatan untuk divisi ini:
                        </label>
                        <div class="positions-container-edit" data-div-id="${divId}">
                            <!-- Positions akan di-load di sini -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="window.tambahFieldPositionEdit(${divId})">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
            
            // Load existing positions untuk division ini
            $.ajax({
                url: '/api/positions?division_id=' + divId,
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        var posContainer = document.querySelector(`.positions-container-edit[data-div-id="${divId}"]`);
                        response.data.forEach(function(position) {
                            window.addExistingPositionEdit(divId, position.id, position.name, posContainer);
                        });
                    }
                }
            });
        };

        // Add existing position ke container
        window.addExistingPositionEdit = function(divId, posId, posName, container) {
            var html = `
                <div class="mb-2 p-2 bg-light border rounded position-field-edit" data-pos-id="${posId}">
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm position-name-edit" value="${posName}" data-original="${posName}" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.position-field-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        };

        // Tambah division baru saat edit
        window.tambahFieldDivisiEdit = function() {
            var container = document.getElementById('containerDivisiEdit');
            var divIndex = container.querySelectorAll('.divisi-wrapper-edit').length;
            
            var html = `
                <div class="mb-3 p-3 border-2 border-primary rounded bg-light divisi-wrapper-edit" data-div-id="new_${divIndex}">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm divisi-name-edit" placeholder="Nama divisi baru" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.divisi-wrapper-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Nested Positions Container -->
                    <div class="ms-3 mb-2 p-3 bg-white rounded border">
                        <label class="form-label small fw-bold mb-2">
                            <i class="bi bi-briefcase me-1"></i>Jabatan untuk divisi ini:
                        </label>
                        <div class="positions-container-edit" data-div-id="new_${divIndex}">
                            <!-- Positions akan ditambah di sini -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="window.tambahFieldPositionEdit('new_${divIndex}')">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        };

        // Tambah position untuk division tertentu saat edit
        window.tambahFieldPositionEdit = function(divId) {
            var container = document.querySelector(`.positions-container-edit[data-div-id="${divId}"]`);
            var html = `
                <div class="mb-2 p-2 bg-light border rounded position-field-edit" data-pos-id="new_pos">
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm position-name-edit" placeholder="Nama jabatan" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.position-field-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        };

        window.updateDept = function() {
            var deptId = document.getElementById('editIdDept').value;
            var deptName = document.getElementById('editNamaDept').value.trim();
            
            if (!deptName) {
                Swal.fire({
                    title: 'Validasi!',
                    text: 'Nama departemen harus diisi',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            console.log('📦 Updating department:', {id: deptId, name: deptName});

            // Update department name
            $.ajax({
                url: '/api/departments/' + deptId,
                type: 'PUT',
                data: JSON.stringify({name: deptName}),
                contentType: 'application/json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    console.log('✅ Department updated');
                    
                    // Process divisions & positions (same like simpanDept)
                    var divWrappers = document.querySelectorAll('#containerDivisiEdit .divisi-wrapper-edit');
                    var allPromises = [];
                    
                    divWrappers.forEach(function(divWrapper) {
                        var divId = divWrapper.getAttribute('data-div-id');
                        var divName = divWrapper.querySelector('.divisi-name-edit').value.trim();
                        
                        if (!divName) return;

                        if (divId.includes('new_')) {
                            // Create new division
                            var divPromise = $.ajax({
                                url: '/api/divisions',
                                type: 'POST',
                                data: JSON.stringify({name: divName, department_id: deptId}),
                                contentType: 'application/json',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                            });

                            allPromises.push(
                                divPromise.then(function(divResponse) {
                                    if (divResponse.data && divResponse.data.id) {
                                        var newDivId = divResponse.data.id;
                                        console.log('✅ Division created:', newDivId);
                                        
                                        // Create positions untuk division ini
                                        var posFields = divWrapper.querySelectorAll('.position-field-edit .position-name-edit');
                                        var posPromises = [];
                                        
                                        posFields.forEach(function(posInput) {
                                            var posName = posInput.value.trim();
                                            if (posName) {
                                                posPromises.push(
                                                    $.ajax({
                                                        url: '/api/positions',
                                                        type: 'POST',
                                                        data: JSON.stringify({name: posName, division_id: newDivId}),
                                                        contentType: 'application/json',
                                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                                    })
                                                );
                                            }
                                        });
                                        
                                        return $.when.apply($, posPromises.length > 0 ? posPromises : [$.when()]);
                                    }
                                })
                            );
                        } else {
                            // Existing division - process position changes
                            var posFields = divWrapper.querySelectorAll('.position-field-edit');
                            
                            posFields.forEach(function(posField) {
                                var posId = posField.getAttribute('data-pos-id');
                                var posName = posField.querySelector('.position-name-edit').value.trim();
                                var original = posField.querySelector('.position-name-edit').getAttribute('data-original');
                                
                                if (posId.includes('new_') && posName) {
                                    // New position
                                    allPromises.push(
                                        $.ajax({
                                            url: '/api/positions',
                                            type: 'POST',
                                            data: JSON.stringify({name: posName, division_id: divId}),
                                            contentType: 'application/json',
                                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                        })
                                    );
                                }
                            });
                        }
                    });

                    // Wait for all operations then show success
                    $.when.apply($, allPromises.length > 0 ? allPromises : [$.when()]).done(function() {
                        console.log('✅ All updates completed!');
                        
                        // Close modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditDept'));
                        if (modal) modal.hide();
                        
                        // Cleanup backdrop
                        document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                            el.remove();
                        });
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        
                        window.onbeforeunload = null;
                        
                        setTimeout(function() {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Departemen berhasil diupdate',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                timer: 2000,
                                timerProgressBar: true,
                                didClose: function() {
                                    window.loadBothTables();
                                }
                            });
                        }, 100);
                    }).fail(function(xhr) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal update data',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal update departemen',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        };

        window.hapusDept = function(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin hapus departemen: ' + nama + ' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Execute delete
                    $.ajax({
                        url: '/api/departments/' + id,
                        type: 'DELETE',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function() {
                            // Bersihkan backdrop
                            document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                                el.remove();
                            });
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            
                            // Matikan beforeunload
                            window.onbeforeunload = null;
                            
                            // Tampilkan notifikasi
                            setTimeout(function() {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Departemen berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didClose: function() {
                                        // Reload both tables via AJAX
                                        window.loadBothTables();
                                    }
                                });
                            }, 100);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal hapus departemen: ' + (xhr.responseJSON?.message || 'Error'),
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
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

        // ===== GLOBAL FUNCTIONS (Outside document.ready for HTML onclick access) =====
        window.openTambahKaryawanModal = function() {
            console.log('🔘 openTambahKaryawanModal dipanggil');
            console.log('📍 Current URL:', window.location.href);
            console.log('🔐 CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
            console.log('jQuery version:', $.fn.jquery);
            
            try {
                $('#formTambahKaryawan')[0].reset();
                $('#imageFotoPreview').hide();
                
                // Get dropdown elements
                var deptSelect = $('#departemenKaryawan');
                var divisionSelect = $('#divisiKaryawan');
                var positionSelect = $('#jabatanKaryawan');
                
                console.log('✅ Form elements found:');
                console.log('   - departemenKaryawan:', deptSelect.length ? 'FOUND' : 'NOT FOUND');
                console.log('   - divisiKaryawan:', divisionSelect.length ? 'FOUND' : 'NOT FOUND');
                console.log('   - jabatanKaryawan:', positionSelect.length ? 'FOUND' : 'NOT FOUND');
                
                // Clear dropdowns
                deptSelect.html('<option value="">-- Loading Departemen... --</option>');
                divisionSelect.html('<option value="">-- Pilih Divisi --</option>');
                positionSelect.html('<option value="">-- Pilih Jabatan --</option>');
                console.log('✅ Dropdowns cleared, now fetching departments...');
                
                // Load departments
                console.log('📥 AJAX CALL START: /api/dropdowns/departments');
                $.ajax({
                    url: '/api/dropdowns/departments',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('✅ AJAX SUCCESS');
                        console.log('Full response:', response);
                        console.log('Response.success:', response.success);
                        console.log('Response.data:', response.data);
                        
                        if (response && response.data && Array.isArray(response.data)) {
                            console.log(`📊 Found ${response.data.length} departments`);
                            
                            if (response.data.length === 0) {
                                console.warn('⚠️ No departments found');
                                deptSelect.html('<option value="">-- Tidak ada departemen --</option>');
                            } else {
                                deptSelect.html('<option value="">-- Pilih Departemen --</option>');
                                response.data.forEach(function(dept, index) {
                                    console.log(`Adding option [${index+1}]: ID=${dept.id}, Name=${dept.name}`);
                                    deptSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
                                });
                                console.log('✅ Total dropdown options:', deptSelect.find('option').length);
                            }
                        } else {
                            console.error('❌ Invalid response format - response.data not array');
                            deptSelect.html('<option value="">-- Invalid response format --</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ AJAX ERROR');
                        console.error('   Status:', xhr.status, xhr.statusText);
                        console.error('   Error text:', error);
                        console.error('   Response:', xhr.responseText.substring(0, 200));
                        deptSelect.html(`<option value="">-- Error ${xhr.status} --</option>`);
                    },
                    complete: function() {
                        console.log('📍 AJAX request complete');
                    }
                });
                
            } catch (e) {
                console.error('🔴 Exception caught:', e.message);
                console.error('Stack:', e.stack);
            }
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('modalTambahKaryawan'));
            modal.show();
            console.log('✅ Modal displayed');
        };

        {{-- ======================================================================== --}}
        {{-- SECTION: JAVASCRIPT - INITIALIZATION & KARYAWAN FUNCTIONS --}}
        {{-- ======================================================================== --}}

        $(document).ready(function() {
            console.log('📄 Document ready - jQuery loaded:', typeof jQuery !== 'undefined');
            
            // ===== SETUP GLOBAL AJAX CONFIG =====
            var baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '').replace(/\/public.*$/, '/public');
            console.log('🌐 Base URL set to:', baseUrl);
            
            $.ajaxSetup({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            });
            
            // Helper function untuk auto-close alert setelah beberapa detik
            function autoCloseAlert(alertSelector, duration) {
                setTimeout(function() {
                    $(alertSelector).fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, duration || 3000); // Default 3 detik
            }
            
            // Check for hash in URL and activate corresponding tab
            function activateTabFromHash() {
                var hash = window.location.hash;
                if (hash) {
                    // Remove '#' from hash
                    var tabId = hash.substring(1);
                    // Find the corresponding tab button
                    var tabButton = document.getElementById(tabId + '-tab');
                    if (tabButton) {
                        // Activate the tab using Bootstrap 5
                        var tab = new bootstrap.Tab(tabButton);
                        tab.show();
                    }
                }
            }

            // Activate tab on page load
            activateTabFromHash();

            // Update hash when tab is clicked
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                var hash = $(e.target).attr('data-bs-target');
                history.pushState(null, null, hash);
            });

            // Activate tab when hash changes (browser back/forward)
            $(window).on('hashchange', function() {
                activateTabFromHash();
            });

            // Function to reload department dropdowns
            function loadDepartemenDropdown() {
                console.log('📂 Loading departments...');
                // Clear existing options (keep placeholder)
                $('#filterDepartemenKaryawan').find('option:not(:first)').remove();
                $('#filterDepartemenMaster').find('option:not(:first)').remove();
                $('#departemenKaryawan').find('option:not(:first)').remove();
                $('#editDepartemenKaryawan').find('option:not(:first)').remove();
                
                // Load departments dari API yang benar
                $.ajax({
                    url: baseUrl + '/api/departments',
                    type: 'GET',
                    success: function(response) {
                        console.log('✅ Departments loaded:', response.data);
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function(dept) {
                                $('#filterDepartemenKaryawan').append('<option value="' + dept.name + '">' + dept.name + '</option>');
                                $('#filterDepartemenMaster').append('<option value="' + dept.name + '">' + dept.name + '</option>');
                                $('#departemenKaryawan').append('<option value="' + dept.id + '">' + dept.name + '</option>');
                                $('#editDepartemenKaryawan').append('<option value="' + dept.id + '">' + dept.name + '</option>');
                            });
                        }
                        
                        console.log('📌 Attaching department change handler...');
                        // Attach event handler untuk departemenKaryawan setelah dropdown ter-load
                        attachDepartmentChangeHandler();
                    },
                    error: function(xhr) {
                        console.error('❌ Error loading departments:', xhr);
                    }
                });
            }
            
            // Function untuk attach event handler untuk department change
            function attachDepartmentChangeHandler() {
                console.log('🔗 Checking for #departemenKaryawan element...');
                var elem = $('#departemenKaryawan');
                console.log('Found element:', elem.length > 0 ? 'YES' : 'NO');
                
                // Hapus handler lama jika ada
                elem.off('change');
                
                // Attach handler baru
                elem.on('change', function(e) {
                    var departmentId = $(this).val();
                    console.log('🔄 Department changed in Tambah Karyawan. Dept ID:', departmentId);
                    console.log('Selected text:', $(this).find('option:selected').text());
                    
                    var jabatanSelect = $('#jabatanKaryawan');
                    var divisiSelect = $('#divisiKaryawan');
                    
                    console.log('Jabatan select found:', jabatanSelect.length > 0 ? 'YES' : 'NO');
                    console.log('Divisi select found:', divisiSelect.length > 0 ? 'YES' : 'NO');

                    if (!departmentId) {
                        console.log('⚠️ No department selected');
                        jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                        divisiSelect.html('<option value="">-- Pilih Divisi --</option>');
                        return;
                    }

                    // Load positions dari API berdasarkan division_id (bukan department_id)
                    console.log('📥 Loading positions for division:', divisionId);
                    // Positions akan diload setelah division dipilih via AJAX
                    // Lihat function: loadPositionsByDivision()

                    // Load divisions dari API berdasarkan department_id
                    console.log('📥 Loading divisions for dept:', departmentId);
                    $.ajax({
                        url: '/api/divisions?department_id=' + departmentId,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ Divisions loaded:', response);
                            divisiSelect.html('<option value="">-- Pilih Divisi --</option>');
                            
                            if (response.success && response.data && response.data.length > 0) {
                                response.data.forEach(function(division) {
                                    divisiSelect.append('<option value="' + division.id + '">' + division.name + '</option>');
                                });
                                console.log('✅ Added ' + response.data.length + ' divisions to dropdown');
                            } else {
                                console.log('⚠️ No divisions found for this department');
                                divisiSelect.html('<option value="">-- Tidak ada Divisi --</option>');
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading divisions:', xhr);
                            divisiSelect.html('<option value="">-- Error loading Divisi --</option>');
                        }
                    });
                });
                
                console.log('✅ Department change handler attached successfully');
                
                // Attach handler untuk division change - load positions ketika division dipilih
                elem.off('change'); // Hapus handler lama jika ada
                $('#divisiKaryawan').off('change');
                $('#divisiKaryawan').on('change', function(e) {
                    var divisionId = $(this).val();
                    console.log('🔄 Division changed. Division ID:', divisionId);
                    loadPositionsByDivision(divisionId);
                });
                
                console.log('✅ Division change handler attached successfully');
            }
            
            // Function to reload division dropdown by department
            function loadDivisiByDepartemen(departmentName) {
                // Clear existing options (keep placeholder)
                $('#filterDivisiKaryawan').find('option:not(:first)').remove();
                
                // If no department selected, don't load divisions
                if (!departmentName) {
                    return;
                }
                
                // Get department ID from department name
                $.ajax({
                    url: '/api/departments/list',
                    success: function(response) {
                        var deptId = null;
                        response.data.forEach(function(dept) {
                            if (dept.name === departmentName) {
                                deptId = dept.id;
                            }
                        });
                        
                        if (!deptId) return;
                        
                        // Load divisions for this department
                        $.ajax({
                            url: '/api/divisions?department_id=' + deptId,
                            success: function(divResponse) {
                                if (divResponse.success && divResponse.data) {
                                    divResponse.data.forEach(function(div) {
                                        $('#filterDivisiKaryawan').append('<option value="' + div.name + '">' + div.name + '</option>');
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Error loading divisions:', xhr);
                            }
                        });
                    }
                });
            }

            // Function to reload position dropdown by division (CHANGED FROM department)
            function loadPositionsByDivision(divId) {
                if (!divId) {
                    divId = $('#divisiKaryawan').val();
                }
                
                if (!divId) {
                    $('#jabatanKaryawan').html('<option value="">-- Pilih Jabatan --</option>');
                    return;
                }
                
                $.ajax({
                    url: '/api/positions?division_id=' + divId,
                    success: function(response) {
                        $('#jabatanKaryawan').html('<option value="">-- Pilih Jabatan --</option>');
                        if (response.success && response.data && response.data.length > 0) {
                            response.data.forEach(function(pos) {
                                $('#jabatanKaryawan').append('<option value="' + pos.id + '">' + pos.name + '</option>');
                            });
                            console.log('✅ Loaded ' + response.data.length + ' positions for division');
                        } else {
                            $('#jabatanKaryawan').html('<option value="">-- Tidak ada Jabatan --</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading positions:', xhr);
                        $('#jabatanKaryawan').html('<option value="">-- Error loading Jabatan --</option>');
                    }
                });
            }

            // Load departments into filter dropdown on page load
            // loadDepartemenDropdown(); // Disabled - tabel sudah load dari API

            // ===== DEBOUNCE HELPER FUNCTION =====
            // Prevent excessive API calls - wait 300ms after user stops typing
            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            // Load and render karyawan table manually (same style as matrix)
            window.loadKaryawanTable = function() {
                const tableBody = document.getElementById('tableKaryawanBody');
                const filterDepartemen = document.getElementById('filterDepartemenKaryawan').value;
                const filterDivisi = document.getElementById('filterDivisiKaryawan').value;
                const searchTerm = document.getElementById('searchKaryawan').value.toLowerCase();
                
                $.ajax({
                    url: '/api/employees',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Filter data berdasarkan department, divisi, dan search
                            let filteredData = response.data;
                            
                            if (filterDepartemen) {
                                filteredData = filteredData.filter(emp => {
                                    return emp.department && emp.department.name === filterDepartemen;
                                });
                            }
                            
                            if (filterDivisi) {
                                filteredData = filteredData.filter(emp => {
                                    return emp.division && emp.division.name === filterDivisi;
                                });
                            }
                            
                            if (searchTerm) {
                                filteredData = filteredData.filter(emp => {
                                    const nikMatch = emp.nik.toLowerCase().includes(searchTerm);
                                    const employeeName = emp.name || emp.nama_karyawan || '';
                                    const nameMatch = employeeName.toLowerCase().includes(searchTerm);
                                    return nikMatch || nameMatch;
                                });
                            }
                            
                            window.renderKaryawanTable(filteredData, tableBody);
                        }
                    },
                    error: function(xhr) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>';
                    }
                });
            };

            // Render karyawan table with manual renumbering
            window.renderKaryawanTable = function(data, tableBody) {
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data karyawan</td></tr>';
                    return;
                }

                // Render rows - renumber based on filtered data
                data.forEach((item, index) => {
                    const employeeName = item.name || item.nama_karyawan || 'N/A';
                    const statusBadge = item.status === 'Aktif' 
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-warning text-dark">Non-Aktif</span>';

                    const deptDivisi = `<small>${item.department ? item.department.name : '-'} / ${item.division ? item.division.name : '-'}</small>`;

                    const row = `
                        <tr>
                            <td style="text-align: center; vertical-align: middle; font-size: 0.85rem; font-weight: 500;">${index + 1}</td>
                            <td style="text-align: center; vertical-align: middle; font-weight: 600; font-size: 0.9rem;">${item.nik}</td>
                            <td style="text-align: left; vertical-align: middle;">${employeeName}</td>
                            <td style="text-align: left; vertical-align: middle;">${deptDivisi}</td>
                            <td style="text-align: center; vertical-align: middle;">${statusBadge}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary btn-edit-karyawan" data-nik="${item.nik}" title="Edit karyawan">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-hapus-karyawan" data-nik="${item.nik}" title="Hapus karyawan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;

                    tableBody.innerHTML += row;
                });

                // Attach event handlers untuk edit button
                document.querySelectorAll('.btn-edit-karyawan').forEach(btn => {
                    btn.addEventListener('click', function() {
                        var nik = this.getAttribute('data-nik');
                        console.log('✏️ Edit button clicked for NIK:', nik);
                        
                        // Fetch karyawan data dan buka modal edit
                        $.ajax({
                            url: '/api/employees/' + nik,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                console.log('✅ Employee data fetched:', response);
                                if (response.data) {
                                    var emp = response.data;
                                    console.log('📋 Populating form with:', emp);
                                    
                                    // Reset file input
                                    document.getElementById('editFotoKaryawan').value = '';
                                    document.getElementById('editImageFotoPreview').style.display = 'none';
                                    
                                    // Store NIK lama dalam hidden field untuk API call
                                    document.getElementById('editKaryawanId').value = emp.nik || '';
                                    
                                    // Populate modal dengan data
                                    document.getElementById('editIdKaryawan').value = emp.nik || '';
                                    document.getElementById('editNamaKaryawan').value = emp.nama_karyawan || emp.name || '';
                                    document.getElementById('editEmailKaryawan').value = emp.email || '';
                                    document.getElementById('editPasswordKaryawan').value = '';
                                    
                                    // Set status radio button
                                    var status = emp.status || 'Aktif';
                                    if (status === 'Aktif' || status === 'aktif') {
                                        document.getElementById('editStatusAktif').checked = true;
                                    } else {
                                        document.getElementById('editStatusNonAktif').checked = true;
                                    }
                                    
                                    // Load foto lama jika ada
                                    if (emp.photo) {
                                        console.log('📷 Photo found:', emp.photo);
                                        document.getElementById('editImageFotoLama').src = '/storage/' + emp.photo;
                                        document.getElementById('editImageFotoLama').style.display = 'block';
                                    } else {
                                        console.log('📷 No photo found');
                                        document.getElementById('editImageFotoLama').style.display = 'none';
                                    }
                                    
                                    // Load departments dropdown
                                    console.log('📂 Loading departments...');
                                    $.ajax({
                                        url: '/api/departments',
                                        type: 'GET',
                                        success: function(deptResponse) {
                                            console.log('✅ Departments loaded:', deptResponse.data);
                                            var deptSelect = document.getElementById('editDepartemenKaryawan');
                                            deptSelect.innerHTML = '<option value="">-- Pilih Departemen --</option>';
                                            
                                            if (deptResponse.data && deptResponse.data.length > 0) {
                                                deptResponse.data.forEach(function(dept) {
                                                    var option = document.createElement('option');
                                                    option.value = dept.id;
                                                    option.textContent = dept.name;
                                                    deptSelect.appendChild(option);
                                                });
                                                console.log('✅ Department options added');
                                                
                                                // Set department value
                                                deptSelect.value = emp.department_id || '';
                                                console.log('📍 Department set to:', emp.department_id);
                                                
                                                // Load divisions untuk department ini
                                                loadDivisionsForEdit(emp.department_id, emp.division_id, emp.position_id);
                                            }
                                        },
                                        error: function(xhr) {
                                            console.error('❌ Error loading departments:', xhr);
                                        }
                                    });
                                    
                                    // Buka modal
                                    console.log('🔓 Opening edit modal...');
                                    var modal = new bootstrap.Modal(document.getElementById('modalEditKaryawan'));
                                    modal.show();
                                } else {
                                    console.error('❌ No data in response');
                                }
                            },
                            error: function(xhr) {
                                console.error('❌ AJAX error:', xhr);
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal memuat data karyawan',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    });
                });
                
                // Helper function untuk load divisions saat edit modal
                function loadDivisionsForEdit(departmentId, divisionId, positionId) {
                    console.log('📊 Loading divisions for edit - Dept:', departmentId, 'Div:', divisionId, 'Pos:', positionId);
                    
                    var divSelect = document.getElementById('editDivisiKaryawan');
                    var posSelect = document.getElementById('editJabatanKaryawan');
                    
                    divSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';
                    posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                    
                    if (!departmentId) {
                        return;
                    }
                    
                    // Load divisions
                    $.ajax({
                        url: '/api/divisions?department_id=' + departmentId,
                        type: 'GET',
                        success: function(response) {
                            console.log('✅ Divisions loaded:', response.data);
                            
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(div) {
                                    var option = document.createElement('option');
                                    option.value = div.id;
                                    option.textContent = div.name;
                                    divSelect.appendChild(option);
                                });
                                
                                // Set division value
                                divSelect.value = divisionId || '';
                                console.log('📍 Division set to:', divisionId);
                                
                                // Load positions untuk division ini
                                if (divisionId) {
                                    loadPositionsForEdit(divisionId, positionId);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading divisions:', xhr);
                        }
                    });
                }
                
                // Helper function untuk load positions saat edit modal
                function loadPositionsForEdit(divisionId, positionId) {
                    console.log('🎯 Loading positions for edit - Div:', divisionId, 'Pos:', positionId);
                    
                    var posSelect = document.getElementById('editJabatanKaryawan');
                    posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                    
                    if (!divisionId) {
                        return;
                    }
                    
                    // Load positions
                    $.ajax({
                        url: '/api/positions?division_id=' + divisionId,
                        type: 'GET',
                        success: function(response) {
                            console.log('✅ Positions loaded:', response.data);
                            
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(pos) {
                                    var option = document.createElement('option');
                                    option.value = pos.id;
                                    option.textContent = pos.name;
                                    posSelect.appendChild(option);
                                });
                                
                                // Set position value
                                posSelect.value = positionId || '';
                                console.log('📍 Position set to:', positionId);
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading positions:', xhr);
                        }
                    });
                }
                
                // Preview foto ketika file dipilih di modal edit
                document.getElementById('editFotoKaryawan').addEventListener('change', function(e) {
                    var file = e.target.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(event) {
                            document.getElementById('editImageFotoPreview').src = event.target.result;
                            document.getElementById('editImageFotoPreview').style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });

                document.querySelectorAll('.btn-hapus-karyawan').forEach(btn => {
                    btn.addEventListener('click', function() {
                        var nik = this.getAttribute('data-nik');
                        hapusKaryawan(nik);
                    });
                });
            };

            // ===== MODAL TAMBAH KARYAWAN EVENT HANDLERS - Backdrop Cleanup =====
            var modalTambahKaryawan = document.getElementById('modalTambahKaryawan');
            if (modalTambahKaryawan) {
                modalTambahKaryawan.addEventListener('hidden.bs.modal', function() {
                    console.log('🔓 Modal Tambah Karyawan ditutup');
                    
                    // Reset form
                    var form = document.getElementById('formTambahKaryawan');
                    if (form) {
                        form.reset();
                    }
                    
                    // Hide preview foto
                    var previewFoto = document.getElementById('imageFotoPreview');
                    if (previewFoto) {
                        previewFoto.style.display = 'none';
                    }
                    
                    // Force cleanup backdrop dan body classes
                    var backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    
                    // Remove modal-open dari body
                    document.body.classList.remove('modal-open');
                    
                    // Remove inline styles pada body
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    console.log('✅ Backdrop dan body classes cleaned up');
                });
            }

            // Filter departemen karyawan dropdown
            $('#filterDepartemenKaryawan').on('change', function() {
                const selectedDept = $(this).val();
                
                // Load divisions for selected department
                if (selectedDept) {
                    loadDivisiByDepartemen(selectedDept);
                } else {
                    // Clear division filter if no department selected
                    $('#filterDivisiKaryawan').find('option:not(:first)').remove();
                }
                
                // Reload table when department filter changes
                window.loadBothTables();
            });

            // Filter divisi karyawan dropdown
            $('#filterDivisiKaryawan').on('change', function() {
                // Reload table when division filter changes
                window.loadBothTables();
            });

            // Helper function to trigger edit modal for employee
            function editKaryawan(id) {
                $(document).trigger('click.edit-karyawan', [id]);
            }

            // Old helper function - no longer used, SweetAlert2 handles delete confirmation now
            function hapusKaryawan(id) {
                // This function is deprecated, use .btn-hapus-karyawan click handler instead
            }

            // Handle reset filter button
            $('#btnResetFilter').on('click', function() {
                console.log('🔄 Reset filter clicked');
                $('#filterDepartemenKaryawan').val('');
                $('#filterDivisiKaryawan').val('');
                $('#searchKaryawan').val('');
                window.loadBothTables();
            });

            // Handle search input with debounce (300ms delay)
            $('#searchKaryawan').on('keyup', debounce(function() {
                var searchTerm = $(this).val().toLowerCase();
                console.log('🔍 Search:', searchTerm);
                window.loadBothTables();
            }, 300));

            // Initialize tableDepartemen (data sudah di-render dari server via Blade)
            var tableDepartemen = $('#tableDepartemen').DataTable({
                language: {
                    url: '{{ asset("assets/datatables/i18n/id.json") }}',
                    "decimal": ",",
                    "emptyTable": "Tidak ada data tersedia",
                    "info": "Menampilkan _START_ ke _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 ke 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "thousands": ".",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada entri yang cocok ditemukan"
                },
                pageLength: 10,
                order: []
            });

            // Assign to window untuk akses global
            window.tableDepartemen = tableDepartemen;

            // Filter departemen dropdown
            $('#filterDepartemenMaster').on('change', function() {
                var selectedDept = $(this).val();
                tableDepartemen.column(1).search(selectedDept).draw();
            });

            $('#tableJabatan').DataTable({
                language: {
                    url: '{{ asset("assets/datatables/i18n/id.json") }}',
                    "decimal": ",",
                    "emptyTable": "Tidak ada data tersedia",
                    "info": "Menampilkan _START_ ke _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 ke 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "infoPostFix": "",
                    "thousands": ".",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada entri yang cocok ditemukan"
                },
                pageLength: 10
            });

            // Positions akan dimuat dinamis dari API berdasarkan Division yang dipilih

            // Preview foto ketika file dipilih
            $('#fotoKaryawan').on('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('#imageFotoPreview').attr('src', event.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Button handlers
            // Handle Tambah Karyawan button click (Backup jQuery handler)
            $('#btnTambahKaryawan').on('click', function(e) {
                e.preventDefault();
                console.log('🔘 jQuery handler: Tombol Tambah Karyawan diklik');
                openTambahKaryawanModal();
            });

            $('#btnSimpanKaryawan').on('click', function() {
                var id = $('#idKaryawan').val();
                var nama = $('#namaKaryawan').val();
                var email = $('#emailKaryawan').val();
                var password = $('#passwordKaryawan').val();
                var departemenId = $('#departemenKaryawan').val();
                var divisiId = $('#divisiKaryawan').val();
                var jabatanId = $('#jabatanKaryawan').val();
                var joinDate = $('#joinDateKaryawan').val();
                var status = $('input[name="statusKaryawan"]:checked').val();
                var fotoFile = $('#fotoKaryawan')[0].files[0];

                if (!id || !nama || !email || !password || !departemenId || !divisiId || !jabatanId || !joinDate || !status) {
                    Swal.fire({
                        title: 'Validasi!',
                        text: 'Semua field harus diisi!',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                var submitBtn = $(this);
                submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass me-1"></i>Menyimpan...');

                // Use FormData untuk handle file upload
                var formData = new FormData();
                formData.append('nik', id);
                formData.append('name', nama);
                formData.append('email', email);
                formData.append('password', password);
                formData.append('department_id', parseInt(departemenId));
                formData.append('division_id', parseInt(divisiId));
                formData.append('position_id', parseInt(jabatanId));
                formData.append('join_date', joinDate);
                formData.append('status', status);
                
                // Add file if exists (opsional)
                if (fotoFile) {
                    formData.append('photo', fotoFile);
                }

                $.ajax({
                    url: '/api/employees',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('✅ Karyawan berhasil disimpan:', response);
                        
                        // LANGSUNG close modal dan reset form (SEBELUM SweetAlert)
                        $('#modalTambahKaryawan').modal('hide');
                        $('#formTambahKaryawan')[0].reset();
                        $('#imageFotoPreview').hide();
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Simpan Data');
                        
                        // Reload table immediately
                        console.log('🔄 Reloading both tables...');
                        window.loadBothTables();
                        
                        // KEMUDIAN show SweetAlert (backdrop sudah hilang)
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data Karyawan berhasil disimpan',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
                    },
                    error: function(xhr) {
                        console.error('❌ Error menyimpan karyawan:', xhr);
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Simpan Data');
                        
                        var errMsg = 'Gagal menyimpan karyawan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            title: 'Gagal!',
                            text: errMsg,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Handle Department dropdown change - populate Division
            $(document).on('change', '#departemenKaryawan', function() {
                var deptId = $(this).val();
                var divisionSelect = $('#divisiKaryawan');
                var positionSelect = $('#jabatanKaryawan');
                
                console.log('🔄 Department changed to:', deptId);
                
                // Clear division dan position
                divisionSelect.html('<option value="">-- Pilih Divisi --</option>');
                positionSelect.html('<option value="">-- Pilih Jabatan --</option>');
                
                if (deptId) {
                    // Load divisions untuk department ini
                    $.ajax({
                        url: '/api/dropdowns/divisions?department_id=' + deptId,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ Divisions loaded for dept', deptId, ':', response);
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(division) {
                                    divisionSelect.append('<option value="' + division.id + '">' + division.name + '</option>');
                                    console.log('➕ Added division:', division.name);
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Error loading divisions:', error, xhr);
                        }
                    });
                }
            });

            // Handle Division dropdown change - populate Position
            $(document).on('change', '#divisiKaryawan', function() {
                var divisionId = $(this).val();
                var positionSelect = $('#jabatanKaryawan');
                
                console.log('🔄 Division changed to:', divisionId);
                
                // Clear position
                positionSelect.html('<option value="">-- Pilih Jabatan --</option>');
                
                if (divisionId) {
                    console.log('📡 Calling API: /api/dropdowns/positions?division_id=' + divisionId);
                    
                    // Load positions untuk division ini
                    $.ajax({
                        url: '/api/dropdowns/positions?division_id=' + divisionId,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ API Response:', response);
                            
                            if (response.success && response.data && response.data.length > 0) {
                                console.log('📊 Total positions:', response.data.length);
                                response.data.forEach(function(position) {
                                    positionSelect.append('<option value="' + position.id + '">' + position.name + '</option>');
                                    console.log('➕ Added position:', position.id, '-', position.name);
                                });
                            } else {
                                console.warn('⚠️ No positions found for division:', divisionId);
                                positionSelect.html('<option value="">-- Tidak ada Jabatan --</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            positionSelect.html('<option value="">-- Error loading positions --</option>');
                        }
                    });
                } else {
                    console.log('⚠️ No division selected');
                }
            });

            // Reset form ketika modal ditutup
            $('#modalTambahKaryawan').on('hide.bs.modal', function() {
                $('#formTambahKaryawan')[0].reset();
            });

            // Handle edit button click
            document.querySelectorAll('.btn-edit-karyawan').forEach(btn => {
                btn.addEventListener('click', function() {
                    var employeeNik = this.getAttribute('data-nik');
                    console.log('🔧 Edit button clicked for:', employeeNik);
                    
                    // Fetch employee data from API
                    $.ajax({
                        url: '/api/employees/' + employeeNik,
                        type: 'GET',
                        success: function(response) {
                            var emp = response.data;
                            console.log('📥 Employee data fetched:', emp);
                            
                            // Populate edit modal with data
                            $('#editKaryawanId').val(emp.nik);
                            $('#editIdKaryawan').val(emp.nik);
                            $('#editNamaKaryawan').val(emp.name || emp.nama_karyawan);
                            $('#editEmailKaryawan').val(emp.email);
                            $('#editPasswordKaryawan').val('');
                            $('#editDepartemenKaryawan').val(emp.department_id);
                            
                            // Trigger change event dengan data untuk initial load
                            $('#editDepartemenKaryawan')
                                .data('isInitialLoad', true)
                                .data('positionId', emp.position_id)
                                .data('divisionId', emp.division_id)
                                .trigger('change');
                            
                            // Wait untuk divisions ter-load, then set values
                            setTimeout(function() {
                                console.log('⏳ Setting position and division values...');
                                $('#editDepartemenKaryawan').data('isInitialLoad', false);
                                
                                var positionId = emp.position_id;
                                var divisionId = emp.division_id;
                                
                                if (positionId && $('#editJabatanKaryawan').val() !== positionId) {
                                    $('#editJabatanKaryawan').val(positionId);
                                }
                                if (divisionId && $('#editDivisiKaryawan').val() !== divisionId) {
                                    $('#editDivisiKaryawan').val(divisionId);
                                }
                                
                                console.log('✅ Position and division set:', {
                                    position: positionId,
                                    division: divisionId
                                });
                            }, 800);
                            
                            // Set status radio button dengan benar
                            if (emp.status === 'Aktif') {
                                $('#editStatusAktif').prop('checked', true);
                                $('#editStatusNonAktif').prop('checked', false);
                            } else if (emp.status === 'Non-Aktif') {
                                $('#editStatusNonAktif').prop('checked', true);
                                $('#editStatusAktif').prop('checked', false);
                            }
                            
                            // Set photo if exists
                            if (emp.photo) {
                                $('#editImageFotoLama').attr('src', '/storage/' + emp.photo).show();
                            } else {
                                $('#editImageFotoLama').hide();
                            }
                            $('#editImageFotoPreview').hide();
                            
                            // Fetch and set competency level
                            $.ajax({
                                url: '/api/competencies?nik=' + employeeNik,
                                type: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(competencyResponse) {
                                    if (competencyResponse.success && competencyResponse.data && competencyResponse.data.length > 0) {
                                        $('#editLevelKompetensi').val(competencyResponse.data[0].level);
                                    } else {
                                        $('#editLevelKompetensi').val('');
                                    }
                                },
                                error: function() {
                                    $('#editLevelKompetensi').val('');
                                }
                            });
                            
                            // Show modal
                            console.log('🔓 Opening edit modal for:', employeeNik);
                            $('#modalEditKaryawan').modal('show');
                        },
                        error: function(xhr) {
                            console.error('❌ Error fetching employee:', xhr);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal memuat data karyawan',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                });
            });

            // Handle Update Karyawan
            $('#btnUpdateKaryawan').on('click', function() {
                var id = $('#editKaryawanId').val();  // NIK lama untuk identify employee
                var nik = $('#editIdKaryawan').val();  // NIK baru (bisa berubah)
                var nama = $('#editNamaKaryawan').val();
                var email = $('#editEmailKaryawan').val();
                var password = $('#editPasswordKaryawan').val();
                var departemenId = $('#editDepartemenKaryawan').val();  // Keep as string first
                var divisiId = $('#editDivisiKaryawan').val();  // Keep as string first
                var jabatanId = $('#editJabatanKaryawan').val();  // Keep as string first
                var status = $('input[name="editStatusKaryawan"]:checked').val();
                var fotoFile = $('#editFotoKaryawan')[0].files[0];

                console.log('🔍 Form values:', {
                    id, nik, nama, email, departemenId, divisiId, jabatanId, status
                });

                // Check if id (NIK lama) is set
                if (!id) {
                    console.error('❌ ERROR: NIK lama (editKaryawanId) tidak ter-set!');
                    var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<strong><i class="bi bi-exclamation-circle"></i> Error!</strong> Data karyawan tidak dapat diidentifikasi. Tutup modal dan buka kembali.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
                    var modalBody = $('#modalEditKaryawan .modal-body');
                    modalBody.prepend(errorHtml);
                    return;
                }

                if (!nik || !nama || !email || !departemenId || !divisiId || !jabatanId) {
                    console.warn('❌ Validation failed - missing required fields');
                    // Show validation error with custom styling
                    var errorHtml = '<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                        '<strong><i class="bi bi-exclamation-triangle"></i> Perhatian!</strong> Semua field harus diisi!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
                    var modalBody = $('#modalEditKaryawan .modal-body');
                    modalBody.prepend(errorHtml);
                    return;
                }

                // Use FormData untuk handle file upload
                var formData = new FormData();
                formData.append('nik', nik);
                formData.append('name', nama);
                formData.append('email', email);
                formData.append('department_id', parseInt(departemenId));
                formData.append('division_id', parseInt(divisiId));
                formData.append('position_id', parseInt(jabatanId));
                formData.append('status', status);

                // Add password only if it's not empty
                if (password && password.length > 0) {
                    formData.append('password', password);
                }

                // Add file if exists (opsional)
                if (fotoFile) {
                    formData.append('photo', fotoFile);
                }

                // Log FormData content untuk debug
                console.log('📤 FormData akan dikirim ke /api/employees/' + id);
                console.log('FormData entries:');
                for (var pair of formData.entries()) {
                    console.log('  ' + pair[0] + ': ' + (pair[1].constructor.name === 'File' ? pair[1].name : pair[1]));
                }

                $.ajax({
                    url: '/api/employees/' + id,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        console.log('✅ Update success:', response);
                        if (response.success) {
                            // Close modal immediately
                            $('#modalEditKaryawan').modal('hide');
                            
                            // Reload tables
                            window.loadBothTables();
                            
                            // Show SweetAlert2 success notification
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data Karyawan berhasil diperbarui',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('❌ UPDATE ERROR:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            response: xhr.responseJSON,
                            responseText: xhr.responseText,
                            requestURL: '/api/employees/' + id
                        });
                        
                        var errorMsg = 'Gagal memperbarui data';
                        
                        // Try to get error message from response
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            if (xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                for (let key in errors) {
                                    if (Array.isArray(errors[key]) && errors[key].length > 0) {
                                        errorMsg = errors[key][0];
                                        break;
                                    }
                                }
                            }
                        }
                        
                        console.log('Error message to show:', errorMsg);
                        
                        // Show error message with custom styling
                        var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong><i class="bi bi-exclamation-circle"></i> Gagal!</strong> ' + errorMsg +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        
                        // Insert alert at the top of modal body
                        var modalBody = $('#modalEditKaryawan .modal-body');
                        modalBody.prepend(errorHtml);
                        
                        // Auto-close error alert after 5 seconds (longer than success)
                        autoCloseAlert(modalBody.find('.alert-danger'), 5000);
                    }
                });
            });

            // Preview foto pada edit modal ketika file dipilih
            $('#editFotoKaryawan').on('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('#editImageFotoPreview').attr('src', event.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Update Jabatan dan Divisi dropdown pada edit modal ketika Departemen berubah
            // Gunakan .off() terlebih dahulu untuk prevent multiple handler attach
            $('#editDepartemenKaryawan').off('change').on('change', function() {
                var departemenId = $(this).val();
                var isInitialLoad = $(this).data('isInitialLoad') === true;
                
                if (!departemenId) {
                    $('#editJabatanKaryawan').html('<option value="">-- Pilih Jabatan --</option>');
                    $('#editDivisiKaryawan').html('<option value="">-- Pilih Divisi --</option>');
                    return;
                }

                var jabatanSelect = $('#editJabatanKaryawan');
                var divisiSelect = $('#editDivisiKaryawan');
                
                // Clear positions - will be loaded when division is selected
                jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                
                // Load divisions berdasarkan department
                $.ajax({
                    url: '/api/divisions?department_id=' + departemenId,
                    context: { isInitialLoad: isInitialLoad, divisionId: $(this).data('divisionId'), divisiSelect: divisiSelect },
                    success: function(response) {
                        divisiSelect.html('<option value="">-- Pilih Divisi --</option>');
                        var divisionId = this.divisionId;
                        
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(div) {
                                divisiSelect.append('<option value="' + div.id + '">' + div.name + '</option>');
                            });
                            // Set division value jika ada dan initial load
                            if (this.isInitialLoad && divisionId) {
                                console.log('📍 Setting division from initial load:', divisionId);
                                divisiSelect.val(divisionId);
                                // Load positions untuk division ini
                                loadPositionsByDivisionEdit(divisionId);
                            }
                        }
                    }
                });
            });
            
            // Handler untuk division change di edit modal - load positions
            $('#editDivisiKaryawan').off('change').on('change', function() {
                var divisionId = $(this).val();
                console.log('🔄 Division changed in edit modal. Division ID:', divisionId);
                loadPositionsByDivisionEdit(divisionId);
            });
            
            // Function untuk load positions by division di edit modal
            function loadPositionsByDivisionEdit(divisionId) {
                var jabatanSelect = $('#editJabatanKaryawan');
                
                if (!divisionId) {
                    jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                    return;
                }
                
                $.ajax({
                    url: '/api/positions?division_id=' + divisionId,
                    success: function(response) {
                        jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(pos) {
                                jabatanSelect.append('<option value="' + pos.id + '">' + pos.name + '</option>');
                            });
                            // Set to initial value jika ada
                            var initialPosId = $('#editDivisiKaryawan').data('positionId');
                            if (initialPosId) {
                                jabatanSelect.val(initialPosId);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading positions:', xhr);
                    }
                });
            }

            // Handle Hapus Karyawan button click
            $(document).on('click', '.btn-hapus-karyawan', function() {
                var row = $(this).closest('tr');
                var nik = $(this).attr('data-nik');
                var nama = row.find('td').eq(3).text(); // Column 3: Nama

                // Show SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Hapus Karyawan?',
                    html: '<strong>' + nama + '</strong><br><small class="text-muted">NIK: ' + nik + '</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading dialog
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Tunggu sebentar, data sedang dihapus...',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Delete the employee
                        $.ajax({
                            url: '/api/employees/' + nik,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Karyawan ' + nama + ' berhasil dihapus.',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        window.loadBothTables(); // Reload both tables
                                    });
                                }
                            },
                            error: function(xhr) {
                                var msg = 'Unknown error';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus karyawan: ' + msg,
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
            });

            // Handle tambah jabatan button
            $('#btnTambahJabatan').on('click', function() {
                var containerJabatan = $('#containerJabatan');
                var newItem = $(`
                    <div class="row mb-2 jabatan-item">
                        <div class="col-10">
                            <input type="text" class="form-control input-jabatan" placeholder="Masukkan nama jabatan" required>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-jabatan">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                containerJabatan.append(newItem);
                updateDeleteButtonVisibility();
            });

            // Handle hapus jabatan button (event delegation)
            $(document).on('click', '.btn-hapus-jabatan', function(e) {
                e.preventDefault();
                $(this).closest('.jabatan-item').remove();
                updateDeleteButtonVisibility();
            });

            // Function to show/hide delete buttons
            function updateDeleteButtonVisibility() {
                var count = $('#containerJabatan .jabatan-item').length;
                $('#containerJabatan .btn-hapus-jabatan').each(function() {
                    $(this).toggle(count > 1);
                });
            }

            // Initialize delete button visibility on page load
            updateDeleteButtonVisibility();

            // ===== HANDLERS UNTUK DEPARTEMEN SECTION SUDAH PINDAH KE GLOBAL FUNCTIONS ATAS =====

            // Function to load existing positions in edit mode
            // Positions are now grouped by divisions, so we load divisions first, then their positions
            function loadExistingPositions(departmentId) {
                // Get baseUrl dari current page location (lebih reliable)
                var baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '').replace(/\/public.*$/, '/public');
                var container = $('#editContainerJabatan');
                container.html('<p class="text-muted">Memuat jabatan...</p>');
                
                // Load all divisions for this department
                $.ajax({
                    url: baseUrl + '/api/divisions?department_id=' + departmentId,
                    success: function(divResponse) {
                        container.html('');
                        
                        if (!divResponse.data || divResponse.data.length === 0) {
                            container.html('<p class="text-muted">Belum ada divisi/jabatan untuk departemen ini</p>');
                            return;
                        }
                        
                        var allPositions = [];
                        var divisionsProcessed = 0;
                        
                        // For each division, load its positions
                        divResponse.data.forEach(function(division) {
                            $.ajax({
                                url: baseUrl + '/api/positions?division_id=' + division.id,
                                success: function(posResponse) {
                                    divisionsProcessed++;
                                    
                                    if (posResponse.data && posResponse.data.length > 0) {
                                        allPositions = allPositions.concat(posResponse.data);
                                    }
                                    
                                    // Once all divisions are processed, display positions
                                    if (divisionsProcessed === divResponse.data.length) {
                                        if (allPositions.length > 0) {
                                            allPositions.forEach(function(position) {
                                                var item = $(`
                                                    <div class="row mb-2 edit-jabatan-item" data-position-id="${position.id}">
                                                        <div class="col-10">
                                                            <input type="text" class="form-control input-edit-jabatan" value="${position.name}" readonly style="background-color: #f8f9fa;">
                                                        </div>
                                                        <div class="col-2 d-flex justify-content-end">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-jabatan-edit">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                `);
                                                container.append(item);
                                            });
                                        } else {
                                            container.html('<p class="text-muted">Belum ada jabatan untuk departemen ini</p>');
                                        }
                                    }
                                },
                                error: function(xhr) {
                                    divisionsProcessed++;
                                    console.error('Error loading positions for division:', xhr);
                                }
                            });
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading divisions:', xhr);
                        $('#editContainerJabatan').html('<p class="text-danger">Gagal memuat jabatan</p>');
                    }
                });
            }

            // --- ALL COMPLEX EDIT MODAL HANDLERS REMOVED ---
            // These were for old modal structure - now using simple onclick

            // ===== MODAL TAMBAH DEPARTEMEN EVENT HANDLERS =====
            // NOTE: Inisialisasi modal sekarang dilakukan di openTambahDeptModal()
            // Event handlers di sini hanya untuk logging dan cleanup tambahan jika diperlukan
            var modalTambahDept = document.getElementById('modalTambahDept');
            if (modalTambahDept) {
                console.log('✅ Setting up event listeners for modalTambahDept');
                
                // Listener saat modal sudah ditampilkan sepenuhnya (untuk logging saja)
                modalTambahDept.addEventListener('shown.bs.modal', function() {
                    console.log('🎬 Modal shown.bs.modal - modal is now fully visible');
                }, false);
                
                // Listener saat modal ditutup - untuk cleanup
                modalTambahDept.addEventListener('hidden.bs.modal', function() {
                    console.log('🔍 Modal hidden.bs.modal event triggered');
                    
                    // Force cleanup backdrop jika masih ada (safety net)
                    setTimeout(function() {
                        var backdrops = document.querySelectorAll('.modal-backdrop');
                        if (backdrops.length > 0) {
                            console.log('⚠️ Found lingering backdrop, cleaning up...');
                            backdrops.forEach(function(el) { el.remove(); });
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                        console.log('✅ Modal hidden cleanup done');
                    }, 100);
                }, false);
                
                console.log('✅ Modal event listeners registered');
            } else {
                console.error('❌ modalTambahDept element not found!');
            }
            
            // ===== MODAL EDIT DEPARTEMEN EVENT HANDLERS =====
            var modalEditDept = document.getElementById('modalEditDept');
            if (modalEditDept) {
                modalEditDept.addEventListener('hidden.bs.modal', function() {
                    // Reset form
                    var form = document.getElementById('formEditDept');
                    if (form) {
                        form.reset();
                    }
                    
                    // Clear containers
                    document.getElementById('containerDivisiEdit').innerHTML = '';
                    document.getElementById('containerJabatanEdit').innerHTML = '';
                    
                    console.log('✅ Modal edit form reset');
                });
            }

            // ===== OVERRIDE PAKSA: Matikan beforeunload event yang bandel =====
            // Solusi "nuklir" untuk menghilangkan notifikasi "Reload site?"
            window.onbeforeunload = null;
            
            window.addEventListener("beforeunload", function (e) {
                // Kosongkan returnValue agar tidak ada notif
                if (e && typeof e.returnValue !== 'undefined') {
                    delete e['returnValue'];
                }
            }, true);  // Gunakan capture phase agar didahulukan

            // Load initial data - AFTER all functions are defined
            window.loadBothTables();

        });
    </script>
@endpush

{{-- Include Department Modals --}}
@include('departments.modals.edit-department')
@include('departments.modals.detail-department')

{{-- Include Employee Modals --}}
@include('employees.modals.edit-employee')


