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
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" id="masterDataTab" role="tablist">
                        {{-- TAB 1: Data Karyawan --}}
                        {{-- Isi: Daftar karyawan dengan NIK, nama, departemen, jabatan, shift, status (Active/Inactive) - support CRUD dan import Excel --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="karyawan-tab" data-bs-toggle="tab"
                                data-bs-target="#karyawan" type="button" role="tab" aria-controls="karyawan"
                                aria-selected="true">
                                <i class="bi bi-people-fill me-2"></i>Data Karyawan
                            </button>
                        </li>
                        {{-- TAB 2: Departemen --}}
                        {{-- Isi: Struktur organisasi dengan departemen untuk organization mapping --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="departemen-tab" data-bs-toggle="tab" data-bs-target="#departemen"
                                type="button" role="tab" aria-controls="departemen" aria-selected="false">
                                <i class="bi bi-building me-2"></i>Departemen
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="masterDataTabContent">
                        {{-- TAB CONTENT 1: Data Karyawan Tab --}}
                        {{-- Fungsi: Menampilkan dan mengelola profil semua karyawan dengan fitur CRUD dan bulk import --}}
                        <!-- Data Karyawan Tab -->
                        <div class="tab-pane fade show active" id="karyawan" role="tabpanel"
                            aria-labelledby="karyawan-tab">
                            <div class="mt-4 px-3">
                                {{-- Header dengan tombol Tambah Karyawan dan Import Excel --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Data Karyawan</h5>
                                    {{-- Support for individual creation dan bulk import via Excel file --}}
                                    <div>
                                        <button id="btnTambahKaryawan" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Karyawan
                                        </button>
                                    </div>
                                </div>

                                {{-- Filter Departemen --}}
                                <div class="mb-3">
                                    <label for="filterDepartemenKaryawan" class="form-label">Filter Departemen:</label>
                                    <select id="filterDepartemenKaryawan" class="form-select">
                                        <option value="">Semua Departemen</option>
                                    </select>
                                </div>

                                {{-- Tabel Data Karyawan --}}
                                {{-- Kolom: No, Foto, NIK, Nama Lengkap, Departemen, Jabatan, Status, Aksi --}}
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped" id="tableKaryawan">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px; text-align: center;">No</th>
                                                <th style="width: 70px; text-align: center;">Foto</th>
                                                <th style="width: 80px; text-align: center;">NIK</th>
                                                <th style="width: auto; text-align: left;">Nama Lengkap</th>
                                                <th style="width: auto; text-align: left;">Departemen</th>
                                                <th style="width: auto; text-align: left;">Jabatan</th>
                                                <th style="width: 100px; text-align: center;">Status</th>
                                                <th style="width: 100px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableKaryawanBody" style="background-color: white;">
                                            {{-- Data loaded dynamically via AJAX from /api/employees --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Departemen Tab -->
                        {{-- TAB CONTENT 2: Departemen Tab --}}
                        {{-- Fungsi: Mengelola struktur organisasi dengan departemen --}}
                        <div class="tab-pane fade" id="departemen" role="tabpanel" aria-labelledby="departemen-tab">
                            <div class="mt-4 px-3">
                                {{-- Header dengan dropdown filter departemen dan tombol Tambah Departemen --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="mb-0">Departemen</h5>
                                        <select class="form-select w-auto" id="filterDepartemenMaster">
                                            <option value="">Semua Departemen</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="btnTambahDepartemen">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Departemen
                                    </button>
                                </div>

                                {{-- Tabel Departemen --}}
                                {{-- Kolom: No (nomor urut), Nama Departemen (department name), Jumlah Karyawan (employee count), Aksi (Edit/Delete) --}}
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped" id="tableDepartemen">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Departemen</th>
                                                <th>Jumlah Karyawan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Data loaded dynamically via AJAX from /api/departments --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Modal Tambah Departemen --}}
        <div class="modal fade" id="modalTambahDepartemen" tabindex="-1" aria-labelledby="modalTambahDepartemenLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahDepartemenLabel">Tambah Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahDepartemen">
                            <div class="mb-3">
                                <label for="namaDepartemen" class="form-label">Nama Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namaDepartemen" placeholder="Masukkan nama departemen baru" required>
                                <small class="text-muted">Contoh: Quality, Maintenance, PPC, Produksi & Dev Engineering, dll.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Daftar Jabatan <span class="text-danger">*</span></label>
                                <div id="containerJabatan">
                                    <div class="row mb-2 jabatan-item">
                                        <div class="col-10">
                                            <input type="text" class="form-control input-jabatan" placeholder="Masukkan nama jabatan (contoh: Manager, Supervisor, Staff)" required>
                                        </div>
                                        <div class="col-2 d-flex justify-content-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-jabatan" style="display: none;">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btnTambahJabatan">
                                    <i class="bi bi-plus me-1"></i>Tambah Jabatan Lagi
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnSimpanDepartemen">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Edit Departemen --}}
        <div class="modal fade" id="modalEditDepartemen" tabindex="-1" aria-labelledby="modalEditDepartemenLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditDepartemenLabel">Edit Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditDepartemen">
                            <input type="hidden" id="editDepartemenId">
                            <div class="mb-3">
                                <label for="editNamaDepartemen" class="form-label">Nama Departemen</label>
                                <input type="text" class="form-control" id="editNamaDepartemen" placeholder="Masukkan nama departemen" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Daftar Jabatan</label>
                                <div id="editContainerJabatan">
                                    <!-- Positions akan dimuat via AJAX -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btnTambahJabatanEdit">
                                    <i class="bi bi-plus me-1"></i>Tambah Jabatan Baru
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnUpdateDepartemen">
                            <i class="bi bi-save me-1"></i>Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus Departemen --}}
        <div class="modal fade" id="modalHapusDepartemen" tabindex="-1" aria-labelledby="modalHapusDepartemenLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalHapusDepartemenLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hapusDepartemenId">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Perhatian!</strong> Data yang dihapus tidak dapat dikembalikan.
                        </div>
                        <p class="mb-2">Anda yakin ingin menghapus departemen berikut?</p>
                        <div class="card bg-light">
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="120"><strong>Departemen:</strong></td>
                                        <td id="hapusDepartemenNama"></td>
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
                        <button type="button" class="btn btn-danger" id="btnKonfirmasiHapus">
                            <i class="bi bi-trash me-1"></i>Ya, Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fotoKaryawan" class="form-label">Foto</label>
                                        <input type="file" class="form-control" id="fotoKaryawan" accept="image/*">
                                        <div id="previewFoto" class="mt-2">
                                            <img id="imageFotoPreview" src="" alt="Preview" style="max-width: 150px; max-height: 150px; display: none; border-radius: 5px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="idKaryawan" class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="idKaryawan" placeholder="Contoh: EMP047" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="namaKaryawan" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namaKaryawan" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emailKaryawan" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="emailKaryawan" placeholder="Masukkan email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="passwordKaryawan" class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="passwordKaryawan" placeholder="Masukkan password" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="departemenKaryawan" class="form-label">Departemen <span class="text-danger">*</span></label>
                                        <select class="form-select" id="departemenKaryawan" required>
                                            <option value="">-- Pilih Departemen --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jabatanKaryawan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="jabatanKaryawan" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="levelKompetensi" class="form-label">Level Kompetensi <span class="text-danger">*</span></label>
                                        <select class="form-select" id="levelKompetensi" required>
                                            <option value="">-- Pilih Level --</option>
                                            <option value="1">L1: Masih perlu dibimbing</option>
                                            <option value="2">L2: Mulai bisa dilepas</option>
                                            <option value="3">L3: Bisa mengerjakan sendiri dgn baik</option>
                                            <option value="4">L4: Bisa mengajarkan ke level rendah</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="statusKaryawan" id="statusAktif" value="active" checked>
                                            <label class="form-check-label" for="statusAktif">
                                                <span class="badge bg-success">Aktif</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="statusKaryawan" id="statusNonAktif" value="inactive">
                                            <label class="form-check-label" for="statusNonAktif">
                                                <span class="badge bg-warning text-dark">Non-Aktif</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSimpanKaryawan">
                            <i class="bi bi-check-circle me-1"></i>Simpan
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
                            <input type="hidden" id="editKaryawanId">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editFotoKaryawan" class="form-label">Foto</label>
                                        <input type="file" class="form-control" id="editFotoKaryawan" accept="image/*">
                                        <div id="editPreviewFoto" class="mt-2">
                                            <img id="editImageFotoPreview" src="" alt="Preview" style="max-width: 150px; max-height: 150px; display: none; border-radius: 5px;">
                                            <img id="editImageFotoLama" src="" alt="Foto Lama" style="max-width: 150px; max-height: 150px; border-radius: 5px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editIdKaryawan" class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editIdKaryawan" placeholder="Contoh: EMP047" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="editNamaKaryawan" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editNamaKaryawan" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEmailKaryawan" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="editEmailKaryawan" placeholder="Masukkan email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editPasswordKaryawan" class="form-label">Password <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                                        <input type="password" class="form-control" id="editPasswordKaryawan" placeholder="Masukkan password baru">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editDepartemenKaryawan" class="form-label">Departemen <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editDepartemenKaryawan" required>
                                            <option value="">-- Pilih Departemen --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editJabatanKaryawan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editJabatanKaryawan" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editLevelKompetensi" class="form-label">Level Kompetensi <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editLevelKompetensi" required>
                                            <option value="">-- Pilih Level --</option>
                                            <option value="1">L1: Masih perlu dibimbing</option>
                                            <option value="2">L2: Mulai bisa dilepas</option>
                                            <option value="3">L3: Bisa mengerjakan sendiri dgn baik</option>
                                            <option value="4">L4: Bisa mengajarkan ke level rendah</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusAktif" value="active">
                                            <label class="form-check-label" for="editStatusAktif">
                                                <span class="badge bg-success">Aktif</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusNonAktif" value="inactive">
                                            <label class="form-check-label" for="editStatusNonAktif">
                                                <span class="badge bg-warning text-dark">Non-Aktif</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-info text-white" id="btnUpdateKaryawan">
                            <i class="bi bi-check-circle me-1"></i>Perbarui
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
    <script>
        $(document).ready(function() {
            console.log('Document ready');
            
            // Helper function untuk auto-close alert setelah beberapa detik
            function autoCloseAlert(alertSelector, duration) {
                setTimeout(function() {
                    $(alertSelector).fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, duration || 3000); // Default 3 detik
            }
            
            // Handler untuk tombol Tambah Karyawan
            $('#btnTambahKaryawan').click(function() {
                console.log('Tombol Tambah Karyawan diklik');
                $('#formTambahKaryawan')[0].reset();
                $('#imageFotoPreview').hide();
                $('#departemenKaryawan').val('');
                $('#jabatanKaryawan').html('<option value="">-- Pilih Jabatan --</option>');
                $('#modalTambahKaryawan').modal('show');
            });
            
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
                // Clear existing options (keep placeholder)
                $('#filterDepartemenKaryawan').find('option:not(:first)').remove();
                $('#filterDepartemenMaster').find('option:not(:first)').remove();
                $('#departemenKaryawan').find('option:not(:first)').remove();
                $('#editDepartemenKaryawan').find('option:not(:first)').remove();
                
                // Load departments into dropdowns
                $.ajax({
                    url: '/api/departments/list',
                    success: function(response) {
                        response.data.forEach(function(dept) {
                            $('#filterDepartemenKaryawan').append('<option value="' + dept.name + '">' + dept.name + '</option>');
                            $('#filterDepartemenMaster').append('<option value="' + dept.name + '">' + dept.name + '</option>');
                            $('#departemenKaryawan').append('<option value="' + dept.id + '">' + dept.name + '</option>');
                            $('#editDepartemenKaryawan').append('<option value="' + dept.id + '">' + dept.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading departments:', xhr);
                    }
                });
            }
            
            // Function to reload position dropdown by department
            function loadPositionsByDepartment(deptId) {
                if (!deptId) {
                    deptId = $('#departemenKaryawan').val();
                }
                
                if (!deptId) return;
                
                $.ajax({
                    url: '/api/positions?department_id=' + deptId,
                    success: function(response) {
                        $('#jabatanKaryawan').html('<option value="">-- Pilih Jabatan --</option>');
                        response.data.forEach(function(pos) {
                            $('#jabatanKaryawan').append('<option value="' + pos.id + '">' + pos.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading positions:', xhr);
                    }
                });
            }

            // Load departments into filter dropdowns on page load
            loadDepartemenDropdown();

            // Load and render karyawan table manually (same style as matrix)
            function loadKaryawanTable() {
                const tableBody = document.getElementById('tableKaryawanBody');
                const filterDepartemen = document.getElementById('filterDepartemenKaryawan').value;
                
                $.ajax({
                    url: '/api/employees',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Filter data berdasarkan department name jika ada filter
                            let filteredData = response.data;
                            if (filterDepartemen) {
                                filteredData = response.data.filter(emp => {
                                    return emp.department && emp.department.name === filterDepartemen;
                                });
                            }
                            renderKaryawanTable(filteredData, tableBody);
                        }
                    },
                    error: function(xhr) {
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Gagal memuat data</td></tr>';
                    }
                });
            }

            // Render karyawan table with manual renumbering
            function renderKaryawanTable(data, tableBody) {
                tableBody.innerHTML = '';

                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Tidak ada data karyawan</td></tr>';
                    return;
                }

                // Render rows - renumber based on filtered data
                data.forEach((item, index) => {
                    const statusBadge = item.status === 'active' 
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-warning text-dark">Non-Aktif</span>';

                    const photoHtml = item.photo_path
                        ? `<img src="${item.photo_path}" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">`
                        : '<i class="bi bi-person-fill" style="font-size: 24px; color: #999;"></i>';

                    const row = `
                        <tr>
                            <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                            <td style="text-align: center; vertical-align: middle;">${photoHtml}</td>
                            <td style="text-align: center; vertical-align: middle;">${item.nik}</td>
                            <td style="text-align: left;">${item.nama_karyawan}</td>
                            <td style="text-align: left;">${item.department ? item.department.name : '-'}</td>
                            <td style="text-align: left;">${item.position ? item.position.name : '-'}</td>
                            <td style="text-align: center;">${statusBadge}</td>
                            <td style="text-align: center;">
                                <button class="btn btn-sm btn-warning btn-edit-karyawan" data-nik="${item.nik}" style="padding: 6px 12px; font-size: 0.85rem;">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-hapus-karyawan" data-nik="${item.nik}" style="padding: 6px 12px; font-size: 0.85rem;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    tableBody.innerHTML += row;
                });

                // Attach event handlers
                document.querySelectorAll('.btn-edit-karyawan').forEach(btn => {
                    btn.addEventListener('click', function() {
                        var nik = this.getAttribute('data-nik');
                        // Trigger the edit modal (this will be handled by existing code)
                        $(this).trigger('click');
                    });
                });

                document.querySelectorAll('.btn-hapus-karyawan').forEach(btn => {
                    btn.addEventListener('click', function() {
                        var nik = this.getAttribute('data-nik');
                        hapusKaryawan(nik);
                    });
                });
            }

            // Load initial data
            loadKaryawanTable();

            // Filter departemen karyawan dropdown
            $('#filterDepartemenKaryawan').on('change', function() {
                // Reload table when department filter changes
                loadKaryawanTable();
            });

            // Helper function to trigger edit modal for employee
            function editKaryawan(id) {
                $(document).trigger('click.edit-karyawan', [id]);
            }

            // Old helper function - no longer used, SweetAlert2 handles delete confirmation now
            function hapusKaryawan(id) {
                // This function is deprecated, use .btn-hapus-karyawan click handler instead
            }

            var tableDepartemen = $('#tableDepartemen').DataTable({
                ajax: {
                    url: '/api/departments',
                    dataSrc: function(json) {
                        return json.data.map(function(dept) {
                            return {
                                'id': dept.id,
                                'nama': dept.name,
                                'jumlah': dept.employees_count,
                                'aksi': '<button class="btn btn-sm btn-warning btn-edit-departemen" data-id="' + dept.id + '" data-departemen="' + dept.name + '"><i class="bi bi-pencil-square"></i></button> <button class="btn btn-sm btn-danger btn-hapus-departemen" data-id="' + dept.id + '" data-departemen="' + dept.name + '"><i class="bi bi-trash"></i></button>'
                            };
                        });
                    }
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
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
                pageLength: 10,
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        width: '60px',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { 
                        targets: 1, 
                        data: 'nama',
                        width: '40%'
                    },
                    { 
                        targets: 2, 
                        data: 'jumlah',
                        width: '20%',
                        className: 'text-end'
                    },
                    {
                        targets: 3,
                        data: 'aksi',
                        orderable: false,
                        searchable: false,
                        width: '150px',
                        className: 'text-end'
                    }
                ],
                order: [],
                drawCallback: function() {
                    // Attach edit/delete handlers after table render
                    $('.btn-edit-departemen').off('click').on('click', function() {
                        var id = $(this).data('id');
                        editDepartemen(id);
                    });
                    $('.btn-hapus-departemen').off('click').on('click', function() {
                        var id = $(this).data('id');
                        hapusDepartemen(id);
                    });
                }
            });

            // Filter departemen dropdown
            $('#filterDepartemenMaster').on('change', function() {
                var selectedDept = $(this).val();
                tableDepartemen.column(1).search(selectedDept).draw();
            });

            $('#tableJabatan').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
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

            // Mapping Jabatan berdasarkan Departemen - FINAL DATA (JANGAN UBAH)
            var jabatanByDepartemen = {
                '1': [
                    'Manager Quality',
                    'Assistant Manager Quality',
                    'Supervisor Quality Control',
                    'QA Engineer',
                    'Foreman QC',
                    'Inspektor QC',
                    'Staff Admin QC',
                    'Staff Admin QA',
                    'Staff HSE'
                ],
                '2': [
                    'Manager Maintenance',
                    'Supervisor Maintenance',
                    'Foreman Maintenance',
                    'Operator Maintenance Listrik',
                    'Operator Maintenance Umum'
                ],
                '3': [
                    'Manager Production, Planning & Control',
                    'Staff PPIC',
                    'Supervisor Gudang',
                    'Foreman Gudang',
                    'Operator Gudang'
                ],
                '4': [
                    'Manager Produksi & Development Engineering',
                    'Supervisor Development Engineer',
                    'Staff Development Engineer',
                    'Supervisor Wax Room',
                    'Foreman Wax Room',
                    'Operator Wax Room',
                    'Supervisor Mould Room',
                    'Foreman Mould Room',
                    'Operator Mould Room',
                    'Supervisor Melting',
                    'Foreman Melting',
                    'Operator Melting',
                    'Supervisor Cut Off',
                    'Foreman Cut Off',
                    'Operator Cut Off',
                    'Supervisor Finishing & Straightening',
                    'Foreman Finishing & Straightening',
                    'Operator Finishing & Straightening',
                    'Supervisor Machining',
                    'Foreman Machining',
                    'Operator Machining'
                ]
            };

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

            // Update Jabatan dropdown ketika Departemen berubah
            $('#departemenKaryawan').on('change', function() {
                var departmentId = $(this).val();
                var jabatanSelect = $('#jabatanKaryawan');

                if (!departmentId) {
                    jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                    return;
                }

                // Load position dari API berdasarkan department_id
                $.ajax({
                    url: '/api/positions?department_id=' + departmentId,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                        
                        if (response.success && response.data && response.data.length > 0) {
                            // Populate dropdown dari API data
                            response.data.forEach(function(position) {
                                jabatanSelect.append('<option value="' + position.id + '">' + position.name + '</option>');
                            });
                        } else {
                            // Jika API tidak return data, tampilkan pesan
                            jabatanSelect.html('<option value="">-- Tidak ada Jabatan --</option>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading positions:', xhr);
                        jabatanSelect.html('<option value="">-- Error loading Jabatan --</option>');
                    }
                });
            });

            // Button handlers
            $('#btnTambahDepartemen').on('click', function() {
                $('#formTambahDepartemen')[0].reset();
                $('#modalTambahDepartemen').modal('show');
            });

            $('#btnSimpanKaryawan').on('click', function() {
                var id = $('#idKaryawan').val();
                var nama = $('#namaKaryawan').val();
                var email = $('#emailKaryawan').val();
                var password = $('#passwordKaryawan').val();
                var departemenId = $('#departemenKaryawan').val();
                var jabatanId = $('#jabatanKaryawan').val();
                var levelKompetensi = $('#levelKompetensi').val();
                var status = $('input[name="statusKaryawan"]:checked').val();

                if (!id || !nama || !email || !password || !departemenId || !jabatanId || !levelKompetensi) {
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

                $.ajax({
                    url: '/api/employees',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        nik: id,
                        nama_karyawan: nama,
                        email: email,
                        password: password,
                        department_id: parseInt(departemenId),
                        position_id: parseInt(jabatanId),
                        status: status || 'active'
                    }),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Save competency level
                            $.ajax({
                                url: '/api/competencies',
                                type: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    nik: id,
                                    level: parseInt(levelKompetensi)
                                }),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(competencyResponse) {
                                    // Show success message
                                    var successHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                        '<strong><i class="bi bi-check-circle"></i> Berhasil!</strong> Data Karyawan dan Level Kompetensi berhasil disimpan.' +
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                        '</div>';
                                    
                                    // Insert alert at the top of modal body
                                    var modalBody = $('#modalTambahKaryawan .modal-body');
                                    modalBody.prepend(successHtml);
                                    
                                    // Auto-close alert after 3 seconds
                                    autoCloseAlert(modalBody.find('.alert-success'));
                                    
                                    // Auto-hide modal and reload table after 5 seconds
                                    setTimeout(function() {
                                        $('#modalTambahKaryawan').modal('hide');
                                        $('#formTambahKaryawan')[0].reset();
                                        loadKaryawanTable();
                                        tableDepartemen.ajax.reload(); // Reload departemen table to update employee count
                                    }, 5000);
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Simpan');
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan: ';
                        for (let key in errors) {
                            errorMsg += errors[key][0] + '\n';
                        }
                        
                        // Show error message with custom styling
                        var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong><i class="bi bi-exclamation-circle"></i> Gagal!</strong> ' + (errorMsg || 'Gagal menyimpan data') +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        
                        var modalBody = $('#modalTambahKaryawan .modal-body');
                        modalBody.prepend(errorHtml);
                        
                        // Auto-close error alert after 3 seconds
                        autoCloseAlert(modalBody.find('.alert-danger'));
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Simpan');
                    }
                });
            });

            // Handle Edit Karyawan button click
            $(document).on('click', '.btn-edit-karyawan', function() {
                var employeeNik = $(this).data('nik');
                
                // Fetch employee data from API
                $.ajax({
                    url: '/api/employees/' + employeeNik,
                    type: 'GET',
                    success: function(response) {
                        var emp = response.data;
                        
                        // Populate edit modal with data
                        $('#editKaryawanId').val(emp.nik);
                        $('#editIdKaryawan').val(emp.nik);
                        $('#editNamaKaryawan').val(emp.nama_karyawan);
                        $('#editEmailKaryawan').val(emp.email);
                        $('#editPasswordKaryawan').val(''); // Clear password field
                        $('#editDepartemenKaryawan').val(emp.department_id);
                        
                        // Trigger change event to load positions
                        $('#editDepartemenKaryawan').trigger('change');
                        
                        // Also set the position after positions are loaded
                        setTimeout(function() {
                            $('#editJabatanKaryawan').val(emp.position_id);
                        }, 500);
                        
                        // Set status radio button
                        if (emp.status === 'active') {
                            $('#editStatusAktif').prop('checked', true);
                        } else {
                            $('#editStatusNonAktif').prop('checked', true);
                        }
                        
                        // Set photo if exists
                        if (emp.photo_path) {
                            $('#editImageFotoLama').attr('src', emp.photo_path).show();
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
                                    $('#editLevelKompetensi').val(''); // Default to empty if no competency found
                                }
                            },
                            error: function() {
                                $('#editLevelKompetensi').val(''); // Default to empty if API fails
                            }
                        });
                        
                        // Show modal
                        $('#modalEditKaryawan').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal memuat data karyawan',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Handle Update Karyawan
            $('#btnUpdateKaryawan').on('click', function() {
                var id = $('#editKaryawanId').val();
                var nik = $('#editIdKaryawan').val();
                var nama = $('#editNamaKaryawan').val();
                var email = $('#editEmailKaryawan').val();
                var password = $('#editPasswordKaryawan').val();
                var departemenId = $('#editDepartemenKaryawan').val();
                var jabatanId = $('#editJabatanKaryawan').val();
                var editLevelKompetensi = $('#editLevelKompetensi').val();
                var status = $('input[name="editStatusKaryawan"]:checked').val();

                if (!nik || !nama || !email || !departemenId || !jabatanId || !editLevelKompetensi) {
                    // Show validation error with custom styling
                    var errorHtml = '<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                        '<strong><i class="bi bi-exclamation-triangle"></i> Perhatian!</strong> Semua field harus diisi!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
                    var modalBody = $('#modalEditKaryawan .modal-body');
                    modalBody.prepend(errorHtml);
                    return;
                }

                var submitData = {
                    nik: nik,
                    nama_karyawan: nama,
                    email: email,
                    department_id: departemenId,
                    position_id: jabatanId,
                    status: status || 'active'
                };

                // Add password only if it's not empty
                if (password && password.length > 0) {
                    submitData.password = password;
                }

                $.ajax({
                    url: '/api/employees/' + id,
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify(submitData),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update competency level
                            $.ajax({
                                url: '/api/competencies',
                                type: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    nik: nik,
                                    level: parseInt(editLevelKompetensi)
                                }),
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(competencyResponse) {
                                    // Show success message with custom styling
                                    var successHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                        '<strong><i class="bi bi-check-circle"></i> Berhasil!</strong> Data Karyawan dan Level Kompetensi berhasil diperbarui.' +
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                        '</div>';
                                    
                                    // Insert alert at the top of modal body
                                    var modalBody = $('#modalEditKaryawan .modal-body');
                                    modalBody.prepend(successHtml);
                                    
                                    // Auto-close alert after 3 seconds
                                    autoCloseAlert(modalBody.find('.alert-success'));
                                    
                                    // Auto-hide modal and reload table after 5 seconds
                                    setTimeout(function() {
                                        $('#modalEditKaryawan').modal('hide');
                                        loadKaryawanTable();
                                        tableDepartemen.ajax.reload(); // Reload departemen table to update employee count
                                    }, 5000);
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan: ';
                        for (let key in errors) {
                            errorMsg += errors[key][0] + '\n';
                        }
                        
                        // Show error message with custom styling
                        var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong><i class="bi bi-exclamation-circle"></i> Gagal!</strong> ' + (errorMsg || 'Gagal memperbarui data') +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>';
                        
                        // Insert alert at the top of modal body
                        var modalBody = $('#modalEditKaryawan .modal-body');
                        modalBody.prepend(errorHtml);
                        
                        // Auto-close error alert after 3 seconds
                        autoCloseAlert(modalBody.find('.alert-danger'));
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

            // Update Jabatan dropdown pada edit modal ketika Departemen berubah
            $('#editDepartemenKaryawan').on('change', function() {
                var departemenId = $(this).val();
                if (!departemenId) return;
                var jabatanSelect = $('#editJabatanKaryawan');
                
                $.ajax({
                    url: '/api/positions?department_id=' + departemenId,
                    success: function(response) {
                        jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                        response.data.forEach(function(pos) {
                            jabatanSelect.append('<option value="' + pos.id + '">' + pos.name + '</option>');
                        });
                    }
                });
            });

            // Handle Hapus Karyawan button click
            $(document).on('click', '.btn-hapus-karyawan', function() {
                var row = $(this).closest('tr');
                var nik = $(this).data('nik');
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
                                        loadKaryawanTable();
                                        tableDepartemen.ajax.reload(); // Reload departemen table to update employee count
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

            $('#btnSimpanDepartemen').on('click', function() {
                var namaDept = $('#namaDepartemen').val();
                var jabatanInputs = $('#containerJabatan .input-jabatan');
                var jabatanList = [];

                if (!namaDept) {
                    Swal.fire({
                        title: 'Validasi!',
                        text: 'Nama departemen harus diisi!',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Collect all positions
                jabatanInputs.each(function() {
                    var value = $(this).val().trim();
                    if (value) {
                        jabatanList.push(value);
                    }
                });

                if (jabatanList.length === 0) {
                    Swal.fire({
                        title: 'Validasi!',
                        text: 'Minimal 1 jabatan harus diisi!',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                var submitBtn = $(this);
                submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass me-1"></i>Menyimpan...');

                // First create department
                $.ajax({
                    url: '/api/departments',
                    type: 'POST',
                    data: {
                        name: namaDept,
                        status: 'active'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(deptResponse) {
                        if (deptResponse.success) {
                            // Create all positions for this department
                            var positionRequests = [];
                            jabatanList.forEach(function(jabatan) {
                                positionRequests.push(
                                    $.ajax({
                                        url: '/api/positions',
                                        type: 'POST',
                                        data: {
                                            name: jabatan,
                                            department_id: deptResponse.data.id,
                                            level: 1,
                                            status: 'active'
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    })
                                );
                            });

                            // Wait for all position requests to complete
                            $.when.apply($, positionRequests).done(function() {
                                submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan');
                                
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Departemen dan ' + jabatanList.length + ' Jabatan berhasil ditambahkan!',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    $('#modalTambahDepartemen').modal('hide');
                                    $('#formTambahDepartemen')[0].reset();
                                    // Reset to default single input
                                    $('#containerJabatan').html(`
                                        <div class="row mb-2 jabatan-item">
                                            <div class="col-10">
                                                <input type="text" class="form-control input-jabatan" placeholder="Masukkan nama jabatan (contoh: Manager, Supervisor, Staff)" required>
                                            </div>
                                            <div class="col-2 d-flex justify-content-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-jabatan" style="display: none;">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `);
                                    tableDepartemen.ajax.reload();
                                    
                                    // Reload department dropdowns in modals
                                    loadDepartemenDropdown();
                                    // Wait a moment then load positions for newly created department
                                    setTimeout(function() {
                                        loadPositionsByDepartment(deptResponse.data.id);
                                    }, 300);
                                });
                            }).fail(function(error) {
                                submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan');
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menambahkan beberapa jabatan',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan');
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan: ';
                        for (let key in errors) {
                            errorMsg += errors[key][0] + '\n';
                        }
                        Swal.fire({
                            title: 'Gagal!',
                            text: errorMsg || 'Gagal menambahkan departemen',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Handle Edit Departemen button click
            $(document).on('click', '.btn-edit-departemen', function() {
                var id = $(this).data('id');
                var departemen = $(this).data('departemen');

                // Populate modal with data
                $('#editDepartemenId').val(id);
                $('#editNamaDepartemen').val(departemen);

                // Load existing positions for this department
                loadExistingPositions(id);

                // Show modal
                $('#modalEditDepartemen').modal('show');
            });

            // Function to load existing positions in edit mode
            function loadExistingPositions(departmentId) {
                $.ajax({
                    url: '/api/positions?department_id=' + departmentId,
                    success: function(response) {
                        var container = $('#editContainerJabatan');
                        container.html('');

                        if (response.success && response.data && response.data.length > 0) {
                            response.data.forEach(function(position) {
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
                    },
                    error: function(xhr) {
                        console.error('Error loading positions:', xhr);
                        $('#editContainerJabatan').html('<p class="text-danger">Gagal memuat jabatan</p>');
                    }
                });
            }

            // Handle tambah jabatan baru button in edit mode
            $('#btnTambahJabatanEdit').on('click', function() {
                var container = $('#editContainerJabatan');
                
                // If container has "Belum ada jabatan" text, replace it
                if (container.find('p').length > 0) {
                    container.html('');
                }

                var newItem = $(`
                    <div class="row mb-2 edit-jabatan-item-new">
                        <div class="col-10">
                            <input type="text" class="form-control input-new-jabatan" placeholder="Masukkan nama jabatan baru" required>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-cancel-jabatan-baru">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                container.append(newItem);
            });

            // Handle delete existing position button
            $(document).on('click', '.btn-hapus-jabatan-edit', function(e) {
                e.preventDefault();
                var item = $(this).closest('.edit-jabatan-item');
                var positionId = item.data('position-id');
                var namaJabatan = item.find('.input-edit-jabatan').val();

                Swal.fire({
                    title: 'Hapus Jabatan?',
                    html: '<strong>' + namaJabatan + '</strong>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/positions/' + positionId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Jabatan berhasil dihapus!',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        item.fadeOut(300, function() {
                                            $(this).remove();
                                            // If no more items, show message
                                            if ($('#editContainerJabatan .edit-jabatan-item').length === 0 && $('#editContainerJabatan .edit-jabatan-item-new').length === 0) {
                                                $('#editContainerJabatan').html('<p class="text-muted">Belum ada jabatan untuk departemen ini</p>');
                                            }
                                        });
                                        // Reload karyawan table since position is deleted
                                        loadKaryawanTable();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus jabatan',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
            });

            // Handle cancel add new position button
            $(document).on('click', '.btn-cancel-jabatan-baru', function(e) {
                e.preventDefault();
                $(this).closest('.edit-jabatan-item-new').remove();
            });

            // Handle Update Departemen
            $('#btnUpdateDepartemen').on('click', function() {
                var id = $('#editDepartemenId').val();
                var namaDept = $('#editNamaDepartemen').val();
                var newJabatanInputs = $('#editContainerJabatan .input-new-jabatan');
                var newJabatanList = [];

                if (!namaDept) {
                    Swal.fire({
                        title: 'Validasi!',
                        text: 'Nama departemen harus diisi!',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Collect new positions
                newJabatanInputs.each(function() {
                    var value = $(this).val().trim();
                    if (value) {
                        newJabatanList.push(value);
                    }
                });

                var submitBtn = $(this);
                submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass me-1"></i>Menyimpan...');

                // First update department name
                $.ajax({
                    url: '/api/departments/' + id,
                    type: 'PUT',
                    data: {
                        name: namaDept,
                        status: 'active'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // If there are new positions to add
                            if (newJabatanList.length > 0) {
                                var positionRequests = [];
                                newJabatanList.forEach(function(jabatan) {
                                    positionRequests.push(
                                        $.ajax({
                                            url: '/api/positions',
                                            type: 'POST',
                                            data: {
                                                name: jabatan,
                                                department_id: id,
                                                level: 1,
                                                status: 'active'
                                            },
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
                                            }
                                        })
                                    );
                                });

                                $.when.apply($, positionRequests).done(function() {
                                    submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Departemen dan ' + newJabatanList.length + ' jabatan baru berhasil disimpan!',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        $('#modalEditDepartemen').modal('hide');
                                        tableDepartemen.ajax.reload();
                                        loadDepartemenDropdown();
                                        loadPositionsByDepartment(id);
                                    });
                                }).fail(function() {
                                    submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
                                    Swal.fire({
                                        title: 'Berhasil Sebagian!',
                                        text: 'Departemen diupdate tapi beberapa jabatan gagal ditambahkan',
                                        icon: 'warning',
                                        confirmButtonColor: '#ffc107'
                                    });
                                });
                            } else {
                                submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Departemen berhasil diperbarui!',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    $('#modalEditDepartemen').modal('hide');
                                    tableDepartemen.ajax.reload();
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan: ';
                        for (let key in errors) {
                            errorMsg += errors[key][0] + '\n';
                        }
                        Swal.fire({
                            title: 'Gagal!',
                            text: errorMsg || 'Gagal memperbarui departemen',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Handle Hapus Departemen button click
            $(document).on('click', '.btn-hapus-departemen', function() {
                var id = $(this).data('id');
                var departemen = $(this).data('departemen');

                // Show SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Hapus Departemen?',
                    html: '<strong>' + departemen + '</strong>',
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

                        // Delete the department
                        $.ajax({
                            url: '/api/departments/' + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Departemen ' + departemen + ' berhasil dihapus.',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        tableDepartemen.ajax.reload();
                                        loadKaryawanTable(); // Reload karyawan table since employees may reference this department
                                        loadDepartemenDropdown(); // Reload dropdowns
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
                                    text: 'Gagal menghapus departemen: ' + msg,
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
