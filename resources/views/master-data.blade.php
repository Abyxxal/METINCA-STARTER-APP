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
                                {{-- Header dengan tombol Tambah Karyawan --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Data Karyawan</h5>
                                    <button id="btnTambahKaryawan" type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan" onclick="openTambahKaryawanModal()">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Karyawan
                                    </button>
                                </div>

                                {{-- Filter Row: Filters + Reset + Search + Import/Export --}}
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
                                            {{-- Dummy Data: Employee 1 --}}
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; font-size: 0.85rem; font-weight: 500;">1</td>
                                                <td style="text-align: center; vertical-align: middle; font-weight: 600; font-size: 0.9rem;">E001</td>
                                                <td style="text-align: left; vertical-align: middle;">Budi Santoso</td>
                                                <td style="text-align: left; vertical-align: middle;"><small>IT / Backend</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <span class="badge bg-success">Aktif</span>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary btn-edit-karyawan" data-nik="E001" data-bs-toggle="modal" data-bs-target="#modalEditEmployee" title="Edit karyawan">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-hapus-karyawan" data-nik="E001" title="Hapus karyawan">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Dummy Data: Employee 2 --}}
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; font-size: 0.85rem; font-weight: 500;">2</td>
                                                <td style="text-align: center; vertical-align: middle; font-weight: 600; font-size: 0.9rem;">E002</td>
                                                <td style="text-align: left; vertical-align: middle;">Siti Nurhaliza</td>
                                                <td style="text-align: left; vertical-align: middle;"><small>HRD / Recruitment</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <span class="badge bg-success">Aktif</span>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary btn-edit-karyawan" data-nik="E002" data-bs-toggle="modal" data-bs-target="#modalEditEmployee" title="Edit karyawan">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-hapus-karyawan" data-nik="E002" title="Hapus karyawan">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Dummy Data: Employee 3 --}}
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; font-size: 0.85rem; font-weight: 500;">3</td>
                                                <td style="text-align: center; vertical-align: middle; font-weight: 600; font-size: 0.9rem;">E003</td>
                                                <td style="text-align: left; vertical-align: middle;">Ahmad Wijaya</td>
                                                <td style="text-align: left; vertical-align: middle;"><small>Finance / Accounting</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <span class="badge bg-success">Aktif</span>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary btn-edit-karyawan" data-nik="E003" data-bs-toggle="modal" data-bs-target="#modalEditEmployee" title="Edit karyawan">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-hapus-karyawan" data-nik="E003" title="Hapus karyawan">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Dummy Data: Employee 4 --}}
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; font-size: 0.85rem; font-weight: 500;">4</td>
                                                <td style="text-align: center; vertical-align: middle; font-weight: 600; font-size: 0.9rem;">E004</td>
                                                <td style="text-align: left; vertical-align: middle;">Rina Setiawan</td>
                                                <td style="text-align: left; vertical-align: middle;"><small>IT / Frontend</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <span class="badge bg-success">Aktif</span>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary btn-edit-karyawan" data-nik="E004" data-bs-toggle="modal" data-bs-target="#modalEditEmployee" title="Edit karyawan">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-hapus-karyawan" data-nik="E004" title="Hapus karyawan">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- Dummy Data: Employee 5 --}}
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle; font-size: 0.85rem; font-weight: 500;">5</td>
                                                <td style="text-align: center; vertical-align: middle; font-weight: 600; font-size: 0.9rem;">E005</td>
                                                <td style="text-align: left; vertical-align: middle;">Rudi Hermawan</td>
                                                <td style="text-align: left; vertical-align: middle;"><small>HRD / Payroll</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <span class="badge bg-warning text-dark">Non-Aktif</span>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary btn-edit-karyawan" data-nik="E005" data-bs-toggle="modal" data-bs-target="#modalEditEmployee" title="Edit karyawan">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-hapus-karyawan" data-nik="E005" title="Hapus karyawan">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
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
                                {{-- Header Section: Title + Search + Button --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Data Departemen</h5>
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="position-relative" style="width: 280px;">
                                            <input type="text" class="form-control form-control-sm" id="searchDepartemen" placeholder="Cari Departemen..." style="padding-right: 35px;">
                                            <i class="bi bi-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" id="btnTambahDepartemen" data-bs-toggle="modal" data-bs-target="#modalTambahDepartemen">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Departemen
                                        </button>
                                    </div>
                                </div>

                                {{-- Table Section with Card Wrapper --}}
                                <div class="card border-0 shadow-sm">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 50px; padding: 1rem 0.75rem;">No</th>
                                                    <th style="padding: 1rem 0.75rem;">Nama Departemen</th>
                                                    <th style="width: 150px; padding: 1rem 0.75rem;">Jumlah Divisi</th>
                                                    <th style="width: 150px; padding: 1rem 0.75rem;">Total Karyawan</th>
                                                    <th style="width: 140px; text-align: center; padding: 1rem 0.75rem;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Dummy Data: IT Department --}}
                                                <tr>
                                                    <td style="padding: 1.2rem 0.75rem;"><span class="fw-bold">1</span></td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="fw-bold" style="font-size: 1.05rem; color: #212529;">Information Technology</span>
                                                    </td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="badge bg-info">3 Divisi</span>
                                                    </td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="badge bg-primary">8 Karyawan</span>
                                                    </td>
                                                    <td style="text-align: center; padding: 1.2rem 0.75rem;">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditDepartment" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info btn-detail-dept" data-dept-id="1" data-dept-name="Information Technology" title="Detail" data-bs-toggle="modal" data-bs-target="#modalDetailDepartment">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Dummy Data: HRD Department --}}
                                                <tr>
                                                    <td style="padding: 1.2rem 0.75rem;"><span class="fw-bold">2</span></td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="fw-bold" style="font-size: 1.05rem; color: #212529;">Human Resources & Development</span>
                                                    </td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="badge bg-info">3 Divisi</span>
                                                    </td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="badge bg-primary">6 Karyawan</span>
                                                    </td>
                                                    <td style="text-align: center; padding: 1.2rem 0.75rem;">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditDepartment" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info btn-detail-dept" data-dept-id="2" data-dept-name="Human Resources & Development" title="Detail" data-bs-toggle="modal" data-bs-target="#modalDetailDepartment">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Dummy Data: Finance Department --}}
                                                <tr>
                                                    <td style="padding: 1.2rem 0.75rem;"><span class="fw-bold">3</span></td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="fw-bold" style="font-size: 1.05rem; color: #212529;">Finance & Accounting</span>
                                                    </td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="badge bg-info">3 Divisi</span>
                                                    </td>
                                                    <td style="padding: 1.2rem 0.75rem;">
                                                        <span class="badge bg-primary">5 Karyawan</span>
                                                    </td>
                                                    <td style="text-align: center; padding: 1.2rem 0.75rem;">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditDepartment" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-info btn-detail-dept" data-dept-id="3" data-dept-name="Finance & Accounting" title="Detail" data-bs-toggle="modal" data-bs-target="#modalDetailDepartment">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" title="Hapus">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Modal Tambah Departemen --}}
        {{-- Modal Tambah Departemen --}}
        <div class="modal fade" id="modalTambahDepartemen" tabindex="-1" aria-labelledby="modalTambahDepartemenLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalTambahDepartemenLabel">
                            <i class="bi bi-building me-2"></i>Tambah Departemen Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4">
                        <form id="formTambahDepartemen">
                            {{-- Input: Nama Departemen --}}
                            <div class="mb-4">
                                <label for="namaDepartemen" class="form-label fw-bold">Nama Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="namaDepartemen" placeholder="Contoh: Information Technology, Human Resources, Finance..." required>
                                <small class="text-muted d-block mt-1">Masukkan nama departemen yang jelas dan deskriptif</small>
                            </div>

                            <hr class="my-4">

                            {{-- Input: Daftar Divisi (Dynamic Repeater) --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Divisi di departemen ini: <span class="text-danger">*</span></label>
                                <small class="text-muted d-block mb-3">Tambahkan divisi/bagian yang ada di departemen ini</small>

                                {{-- Divisi Container --}}
                                <div id="divisiContainer" class="mb-3">
                                    {{-- Dummy Row 1 --}}
                                    <div class="divisi-item mb-2 p-3 border rounded" style="background-color: #f8f9fa;">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-10">
                                                <input type="text" class="form-control form-control-sm" placeholder="Nama divisi (contoh: Backend, Frontend, DevOps)" value="Backend Division" required>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-divisi" title="Hapus divisi">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Dummy Row 2 --}}
                                    <div class="divisi-item mb-2 p-3 border rounded" style="background-color: #f8f9fa;">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-10">
                                                <input type="text" class="form-control form-control-sm" placeholder="Nama divisi (contoh: Backend, Frontend, DevOps)" value="Frontend Division" required>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-divisi" title="Hapus divisi">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Dummy Row 3 --}}
                                    <div class="divisi-item mb-2 p-3 border rounded" style="background-color: #f8f9fa;">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-10">
                                                <input type="text" class="form-control form-control-sm" placeholder="Nama divisi (contoh: Backend, Frontend, DevOps)" value="DevOps Division" required>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-divisi" title="Hapus divisi">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Button: Add More Divisi --}}
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnAddDivisiItem">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Divisi Lain
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSimpanDepartemen">
                            <i class="bi bi-check-circle me-2"></i>Simpan
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

                            <hr>

                            <div class="mb-3">
                                <label class="form-label">Daftar Divisi</label>
                                <div id="editContainerDivisi">
                                    <!-- Divisions akan dimuat via AJAX -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-info mt-2" id="btnTambahDivisiEdit">
                                    <i class="bi bi-plus me-1"></i>Tambah Divisi Baru
                                </button>
                            </div>

                            <hr>
                            
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
                                        <label for="passwordKaryawan" class="form-label fw-bold">Password Default <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="passwordKaryawan" placeholder="Masukkan password" value="12345678" readonly style="background-color: #f8f9fa;">
                                        <small class="text-muted d-block mt-1">Default: <strong>12345678</strong> (Karyawan dapat mengubahnya setelah login pertama)</small>
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

                                    {{-- Upload Photo --}}
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
                                        <input type="text" class="form-control form-control-edit" id="editIdKaryawan" placeholder="Contoh: EMP047" required autocomplete="off">
                                        <small class="text-muted d-block mt-1">✏️ Field ini dapat diubah</small>
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
                                        <label for="editDivisiKaryawan" class="form-label">Divisi <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editDivisiKaryawan" required>
                                            <option value="">-- Pilih Divisi --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global error handler
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('❌ GLOBAL ERROR:', msg, 'at', url, ':', lineNo);
            return false;
        };

        $(document).ready(function() {
            console.log('📄 Document ready at', new Date().toLocaleTimeString());
            
            // Function untuk open modal tambah karyawan
            window.openTambahKaryawanModal = function() {
                console.log('🔘 openTambahKaryawanModal dipanggil');
                $('#formTambahKaryawan')[0].reset();
                $('#imageFotoPreview').hide();
                var modal = new bootstrap.Modal(document.getElementById('modalTambahKaryawan'));
                modal.show();
                console.log('✅ Modal Tambah Karyawan opened with Bootstrap API');
            };
            
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
                
                // Load departments into dropdowns
                $.ajax({
                    url: '/api/departments/list',
                    success: function(response) {
                        console.log('✅ Departments loaded:', response.data);
                        response.data.forEach(function(dept) {
                            $('#filterDepartemenKaryawan').append('<option value="' + dept.name + '">' + dept.name + '</option>');
                            $('#filterDepartemenMaster').append('<option value="' + dept.name + '">' + dept.name + '</option>');
                            $('#departemenKaryawan').append('<option value="' + dept.id + '">' + dept.name + '</option>');
                            $('#editDepartemenKaryawan').append('<option value="' + dept.id + '">' + dept.name + '</option>');
                        });
                        
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

                    // Load positions dari API berdasarkan department_id
                    console.log('📥 Loading positions for dept:', departmentId);
                    $.ajax({
                        url: '/api/positions?department_id=' + departmentId,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ Positions loaded:', response);
                            jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                            
                            if (response.success && response.data && response.data.length > 0) {
                                response.data.forEach(function(position) {
                                    jabatanSelect.append('<option value="' + position.id + '">' + position.name + '</option>');
                                });
                                console.log('✅ Added ' + response.data.length + ' positions to dropdown');
                            } else {
                                console.log('⚠️ No positions found for this department');
                                jabatanSelect.html('<option value="">-- Tidak ada Jabatan --</option>');
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading positions:', xhr);
                            jabatanSelect.html('<option value="">-- Error loading Jabatan --</option>');
                        }
                    });

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
            }
                                });
                            } else {
                                divisiSelect.html('<option value="">-- Tidak ada Divisi --</option>');
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading divisions:', xhr);
                            divisiSelect.html('<option value="">-- Error loading Divisi --</option>');
                        }
                    });
                });
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

            // Load departments into filter dropdown on page load
            loadDepartemenDropdown();

            // Load and render karyawan table manually (same style as matrix)
            function loadKaryawanTable() {
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
                                    const nameMatch = emp.nama_karyawan.toLowerCase().includes(searchTerm);
                                    return nikMatch || nameMatch;
                                });
                            }
                            
                            renderKaryawanTable(filteredData, tableBody);
                        }
                    },
                    error: function(xhr) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data</td></tr>';
                    }
                });
            }

            // Render karyawan table with manual renumbering
            function renderKaryawanTable(data, tableBody) {
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data karyawan</td></tr>';
                    return;
                }

                // Render rows - renumber based on filtered data
                data.forEach((item, index) => {
                    const statusBadge = item.status === 'active' 
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-warning text-dark">Non-Aktif</span>';

                    const deptDivisi = `<small>${item.department ? item.department.name : '-'} / ${item.division ? item.division.name : '-'}</small>`;

                    const row = `
                        <tr>
                            <td style="text-align: center; vertical-align: middle; font-size: 0.85rem; font-weight: 500;">${index + 1}</td>
                            <td style="text-align: center; vertical-align: middle; font-weight: 600; font-size: 0.9rem;">${item.nik}</td>
                            <td style="text-align: left; vertical-align: middle;">${item.nama_karyawan}</td>
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
                                    <button class="btn btn-outline-secondary btn-reset-pass" data-nik="${item.nik}" title="Reset password">
                                        <i class="bi bi-key"></i>
                                    </button>
                                </div>
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

                // Handler untuk reset password button
                document.querySelectorAll('.btn-reset-pass').forEach(btn => {
                    btn.addEventListener('click', function() {
                        var nik = this.getAttribute('data-nik');
                        resetPasswordKaryawan(nik);
                    });
                });
            }

            // Load initial data
            loadKaryawanTable();

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
                loadKaryawanTable();
            });

            // Filter divisi karyawan dropdown
            $('#filterDivisiKaryawan').on('change', function() {
                // Reload table when division filter changes
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

            // Handle reset filter button
            $('#btnResetFilter').on('click', function() {
                console.log('🔄 Reset filter clicked');
                $('#filterDepartemenKaryawan').val('');
                $('#filterDivisiKaryawan').val('');
                $('#searchKaryawan').val('');
                loadKaryawanTable();
            });

            // Handle search input
            $('#searchKaryawan').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                console.log('🔍 Search:', searchTerm);
                loadKaryawanTable();
            });

            // Function untuk reset password karyawan
            function resetPasswordKaryawan(nik) {
                console.log('🔑 Reset password untuk:', nik);
                Swal.fire({
                    title: 'Reset Password?',
                    text: 'Password akan direset ke password default untuk karyawan ' + nik,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call API to reset password
                        $.ajax({
                            url: '/api/employees/' + nik + '/reset-password',
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message || 'Password berhasil direset',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal mereset password',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
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

            // Button handlers
            $('#btnTambahDepartemen').on('click', function() {
                $('#formTambahDepartemen')[0].reset();
                
                // Reset divisi container with 1 empty input
                $('#divisiContainer').html(`
                    <div class="row mb-2 divisi-item">
                        <div class="col-10">
                            <input type="text" class="form-control divisi-name" placeholder="Masukkan nama divisi" required>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-divisi" style="display: none;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                
                // Reset jabatan container
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
                
                $('#modalTambahDepartemen').modal('show');
            });

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
                var levelKompetensi = $('#levelKompetensi').val();
                var status = $('input[name="statusKaryawan"]:checked').val();

                if (!id || !nama || !email || !password || !departemenId || !divisiId || !jabatanId || !levelKompetensi) {
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
                        division_id: parseInt(divisiId),
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
                var employeeNik = $(this).attr('data-nik');
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
                        $('#editNamaKaryawan').val(emp.nama_karyawan);
                        $('#editEmailKaryawan').val(emp.email);
                        $('#editPasswordKaryawan').val(''); // Clear password field
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
                            // Clear flag setelah timeout
                            $('#editDepartemenKaryawan').data('isInitialLoad', false);
                            
                            // Set values (ini adalah fallback, seharusnya sudah di-set oleh ajax success)
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

            // Handle Update Karyawan
            $('#btnUpdateKaryawan').on('click', function() {
                var id = $('#editKaryawanId').val();  // NIK lama untuk identify employee
                var nik = $('#editIdKaryawan').val();  // NIK baru (bisa berubah)
                var nama = $('#editNamaKaryawan').val();
                var email = $('#editEmailKaryawan').val();
                var password = $('#editPasswordKaryawan').val();
                var departemenId = parseInt($('#editDepartemenKaryawan').val());
                var divisiId = parseInt($('#editDivisiKaryawan').val());
                var jabatanId = parseInt($('#editJabatanKaryawan').val());
                var editLevelKompetensi = parseInt($('#editLevelKompetensi').val());
                var status = $('input[name="editStatusKaryawan"]:checked').val();

                console.log('🔍 Form values before validation:', {
                    id, nik, nama, email, departemenId, divisiId, jabatanId, editLevelKompetensi, status
                });

                if (!nik || !nama || !email || !departemenId || !divisiId || !jabatanId || !editLevelKompetensi) {
                    console.warn('❌ Validation failed - missing required fields');
                    // Show validation error with custom styling
                    var errorHtml = '<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
                        '<strong><i class="bi bi-exclamation-triangle"></i> Perhatian!</strong> Semua field harus diisi! (NIK: ' + nik + ', Nama: ' + nama + ', Email: ' + email + ', Dept: ' + departemenId + ', Div: ' + divisiId + ', Jab: ' + jabatanId + ', Level: ' + editLevelKompetensi + ')' +
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
                    division_id: divisiId,
                    position_id: jabatanId,
                    status: status || 'active'
                };

                // Add password only if it's not empty
                if (password && password.length > 0) {
                    submitData.password = password;
                }

                console.log('📤 Updating employee with data:', submitData);

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
                                    nik: nik,  // Use new NIK for competency update
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
                        console.log('❌ UPDATE ERROR:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            response: xhr.responseJSON,
                            responseText: xhr.responseText
                        });
                        
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
                
                // Load positions
                $.ajax({
                    url: '/api/positions?department_id=' + departemenId,
                    success: function(response) {
                        jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(pos) {
                                jabatanSelect.append('<option value="' + pos.id + '">' + pos.name + '</option>');
                            });
                        }
                    }
                });
                
                // Load divisions
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
                            }
                        } else {
                            // Jika tidak ada divisi untuk department ini, load semua divisions sebagai fallback
                            $.ajax({
                                url: '/api/divisions',
                                context: { divisionId: divisionId, divisiSelect: divisiSelect, isInitialLoad: this.isInitialLoad },
                                success: function(allResponse) {
                                    if (allResponse.data && allResponse.data.length > 0) {
                                        allResponse.data.forEach(function(div) {
                                            this.divisiSelect.append('<option value="' + div.id + '">' + div.name + '</option>');
                                        }.bind(this));
                                        // Set division value dari data attribute
                                        if (this.isInitialLoad && this.divisionId) {
                                            console.log('📍 Setting division from fallback:', this.divisionId);
                                            this.divisiSelect.val(this.divisionId);
                                        }
                                    }
                                }
                            });
                        }
                    }.bind({ isInitialLoad: isInitialLoad, divisionId: $(this).data('divisionId'), divisiSelect: divisiSelect })
                });
            });

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
                var divisiInputs = $('#divisiContainer .divisi-name');
                var jabatanList = [];
                var divisiList = [];

                console.log('=== SIMPAN DEPARTEMEN ===');
                console.log('📝 Nama Dept:', namaDept);
                console.log('🔍 Jabatan inputs found:', jabatanInputs.length);
                console.log('🔍 Divisi inputs found:', divisiInputs.length);

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
                        console.log('  ✅ Jabatan:', value);
                    }
                });

                // Collect all divisions
                divisiInputs.each(function() {
                    var nilai = $(this).val().trim();
                    if (nilai) {
                        divisiList.push({
                            name: nilai,
                            description: ''
                        });
                        console.log('  ✅ Divisi:', nilai);
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

                if (divisiList.length === 0) {
                    Swal.fire({
                        title: 'Validasi!',
                        text: 'Minimal 1 divisi harus diisi!',
                        icon: 'warning',
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                console.log('📊 Total Jabatan:', jabatanList.length);
                console.log('📊 Total Divisi:', divisiList.length);

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
                        console.log('✅ Department created:', deptResponse.data);
                        if (deptResponse.success) {
                            var deptId = deptResponse.data.id;
                            var allRequests = [];

                            // Create all positions for this department
                            jabatanList.forEach(function(jabatan) {
                                console.log('📤 POST /api/positions:', jabatan);
                                allRequests.push(
                                    $.ajax({
                                        url: '/api/positions',
                                        type: 'POST',
                                        data: {
                                            name: jabatan,
                                            department_id: deptId,
                                            level: 1,
                                            status: 'active'
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    })
                                );
                            });

                            // Create all divisions for this department
                            console.log('=== CREATING DIVISIONS ===');
                            divisiList.forEach(function(divisi) {
                                console.log('📤 POST /api/divisions:', divisi.name);
                                allRequests.push(
                                    $.ajax({
                                        url: '/api/divisions',
                                        type: 'POST',
                                        data: {
                                            department_id: deptId,
                                            name: divisi.name,
                                            description: divisi.description,
                                            status: 'active'
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        dataType: 'json',
                                        success: function(resp) {
                                            console.log('✅ Division created:', divisi.name, 'ID:', resp.data.id);
                                        },
                                        error: function(err) {
                                            console.error('❌ Division creation failed:', divisi.name);
                                            console.error('Response:', err.responseJSON);
                                        }
                                    })
                                );
                            });

                            // Wait for all requests to complete
                            $.when.apply($, allRequests).done(function() {
                                submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan');
                                
                                console.log('✅ ALL REQUESTS COMPLETED');
                                
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Departemen, ' + jabatanList.length + ' Jabatan, dan ' + divisiList.length + ' Divisi berhasil ditambahkan!',
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
                                    initDivisiContainer();
                                    tableDepartemen.ajax.reload();
                                    
                                    // Reload department dropdowns in modals
                                    loadDepartemenDropdown();
                                    // Wait a moment then load positions for newly created department
                                    setTimeout(function() {
                                        loadPositionsByDepartment(deptId);
                                    }, 300);
                                });
                            }).fail(function(error) {
                                submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan');
                                console.error('❌ Some requests failed');
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menambahkan beberapa item',
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
                        console.error('❌ Department creation failed:', errorMsg);
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
                
                console.log('📝 Edit Departemen clicked:', {id: id, name: departemen});

                // Populate modal with data
                $('#editDepartemenId').val(id);
                $('#editNamaDepartemen').val(departemen);

                // Load existing positions and divisions for this department
                console.log('⏳ Loading positions and divisions for department', id);
                loadExistingPositions(id);
                loadExistingDivisions(id);

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

            // Handle tambah jabatan baru button in edit mode - USE EVENT DELEGATION
            $(document).on('click', '#btnTambahJabatanEdit', function() {
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

            // Handle Update Departemen - REMOVED (using new event delegated handler instead)

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

            // ===== DIVISI HANDLERS =====

            // Tambah Divisi Item
            $(document).on('click', '#btnAddDivisiItem', function(e) {
                e.preventDefault();
                console.log('➕ Add divisi item clicked');
                var container = $('#divisiContainer');
                
                var newItem = $(`
                    <div class="row mb-2 divisi-item">
                        <div class="col-10">
                            <input type="text" class="form-control divisi-name" placeholder="Masukkan nama divisi" required>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-divisi">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                container.append(newItem);
                updateDivisiButtons();
            });

            // Hapus Divisi Item
            $(document).on('click', '.btn-remove-divisi', function(e) {
                e.preventDefault();
                console.log('❌ Remove divisi item clicked');
                $(this).closest('.divisi-item').remove();
                updateDivisiButtons();
            });

            // Update visibility dari delete button
            function updateDivisiButtons() {
                var items = $('#divisiContainer .divisi-item');
                if (items.length > 1) {
                    items.find('.btn-remove-divisi').show();
                } else {
                    items.find('.btn-remove-divisi').hide();
                }
            }

            // Initialize divisi container with default item
            function initDivisiContainer() {
                console.log('🔄 initDivisiContainer called');
                $('#divisiContainer').html(`
                    <div class="row mb-2 divisi-item">
                        <div class="col-10">
                            <input type="text" class="form-control divisi-name" placeholder="Masukkan nama divisi" required>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-divisi" style="display: none;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                updateDivisiButtons();
            }

            // ===== DIVISION MANAGEMENT IN EDIT MODE =====

            function loadExistingDivisions(departmentId) {
                console.log('🔍 loadExistingDivisions called with department ID:', departmentId);
                $.ajax({
                    url: '/api/divisions?department_id=' + departmentId,
                    success: function(response) {
                        console.log('✅ Division API response:', response);
                        var container = $('#editContainerDivisi');
                        container.html('');

                        if (response.success && response.data && response.data.length > 0) {
                            console.log('📋 Found', response.data.length, 'divisions');
                            response.data.forEach(function(division) {
                                console.log('  - Division:', division.id, division.name);
                                var item = $(`
                                    <div class="row mb-2 edit-divisi-item" data-division-id="${division.id}">
                                        <div class="col-10">
                                            <input type="text" class="form-control input-edit-divisi" value="${division.name}" readonly style="background-color: #f8f9fa;">
                                        </div>
                                        <div class="col-2 d-flex justify-content-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-divisi-edit">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                `);
                                container.append(item);
                            });
                        } else {
                            console.log('⚠️ No divisions found or empty response');
                            container.html('<p class="text-muted">Belum ada divisi untuk departemen ini</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error loading divisions:', status, error);
                        console.error('❌ XHR:', xhr.status, xhr.responseText);
                        $('#editContainerDivisi').html('<p class="text-danger">Gagal memuat divisi (Error: ' + xhr.status + ')</p>');
                    }
                });
            }

            // Handle tambah divisi baru button in edit mode
            $(document).on('click', '#btnTambahDivisiEdit', function() {
                console.log('🔘 Tambah Divisi Edit button clicked');
                var container = $('#editContainerDivisi');
                
                // If container has "Belum ada divisi" text, replace it
                if (container.find('p').length > 0) {
                    container.html('');
                }

                var newItem = $(`
                    <div class="row mb-2 edit-divisi-item-new">
                        <div class="col-10">
                            <input type="text" class="form-control input-new-divisi" placeholder="Masukkan nama divisi baru" required>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-cancel-divisi-baru">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                container.append(newItem);
            });

            // Handle delete existing division button
            $(document).on('click', '.btn-hapus-divisi-edit', function(e) {
                e.preventDefault();
                var item = $(this).closest('.edit-divisi-item');
                var divisionId = item.data('division-id');
                var namaDivisi = item.find('.input-edit-divisi').val();

                Swal.fire({
                    title: 'Hapus Divisi?',
                    html: '<strong>' + namaDivisi + '</strong>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/api/divisions/' + divisionId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name=\"csrf-token\"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Divisi berhasil dihapus!',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    });
                                    item.remove();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus divisi',
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
            });

            // Handle cancel add new division
            $(document).on('click', '.btn-cancel-divisi-baru', function(e) {
                e.preventDefault();
                $(this).closest('.edit-divisi-item-new').remove();
            });

            // Update handler untuk mencakup divisions
            $(document).on('click', '#btnUpdateDepartemen', function(e) {
                e.preventDefault();
                console.log('Update departemen button clicked');
                
                var id = $('#editDepartemenId').val();
                var namaDept = $('#editNamaDepartemen').val();
                var newJabatanInputs = $('#editContainerJabatan .input-new-jabatan');
                var newJabatanList = [];
                var newDivisiInputs = $('#editContainerDivisi .input-new-divisi');
                var newDivisiList = [];

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

                // Collect new divisions (unsaved)
                newDivisiInputs.each(function() {
                    var nilai = $(this).val().trim();
                    if (nilai) {
                        newDivisiList.push({
                            name: nilai,
                            description: ''
                        });
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
                        console.log('Department updated:', response);
                        if (response.success) {
                            var allRequests = [];

                            // Create new positions
                            newJabatanList.forEach(function(jabatan) {
                                allRequests.push(
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
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    })
                                );
                            });

                            // Create new divisions
                            newDivisiList.forEach(function(divisi) {
                                allRequests.push(
                                    $.ajax({
                                        url: '/api/divisions',
                                        type: 'POST',
                                        data: {
                                            department_id: id,
                                            name: divisi.name,
                                            description: divisi.description,
                                            status: 'active'
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        }
                                    })
                                );
                            });

                            if (allRequests.length > 0) {
                                $.when.apply($, allRequests).done(function() {
                                    console.log('All items created successfully');
                                    submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
                                    var msg = 'Departemen berhasil diperbarui';
                                    if (newJabatanList.length > 0) msg += ' dan ' + newJabatanList.length + ' jabatan ditambahkan';
                                    if (newDivisiList.length > 0) msg += ' dan ' + newDivisiList.length + ' divisi ditambahkan';
                                    
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: msg,
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        $('#modalEditDepartemen').modal('hide');
                                        tableDepartemen.ajax.reload();
                                        loadDepartemenDropdown();
                                        loadPositionsByDepartment(id);
                                    });
                                }).fail(function() {
                                    console.log('Some items failed to create');
                                    submitBtn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
                                    Swal.fire({
                                        title: 'Berhasil Sebagian!',
                                        text: 'Departemen diupdate tapi beberapa item gagal ditambahkan',
                                        icon: 'warning',
                                        confirmButtonColor: '#ffc107'
                                    });
                                });
                            } else {
                                console.log('No new items to create');
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
                            text: errorMsg || 'Gagal mengupdate departemen',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Update edit modal opening to load divisions
            $(document).on('click', '.btn-edit-departemen', function() {
                var id = $(this).data('id');
                var departemen = $(this).data('departemen');

                // Populate modal with data
                $('#editDepartemenId').val(id);
                $('#editNamaDepartemen').val(departemen);

                // Load existing positions for this department
                loadExistingPositions(id);
                
                // Load existing divisions for this department
                loadExistingDivisions(id);

                // Show modal
                $('#modalEditDepartemen').modal('show');
            });
        });
    </script>
@endpush

{{-- Include Department Modals --}}
@include('departments.modals.edit-department')
@include('departments.modals.detail-department')

{{-- Include Employee Modals --}}
@include('employees.modals.edit-employee')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle detail department button click
        document.querySelectorAll('.btn-detail-dept').forEach(btn => {
            btn.addEventListener('click', function() {
                const deptName = this.getAttribute('data-dept-name');
                document.getElementById('detailDeptName').textContent = deptName;
            });
        });
    });
</script>
@endpush
