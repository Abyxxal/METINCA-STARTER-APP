{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Settings')

{{-- Untuk menggunakan css --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
@endpush

{{-- Isi content --}}
@section('content')

    {{-- SECTION: Page Header --}}
    {{-- Fungsi: Menampilkan judul halaman Settings dan breadcrumb --}}
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Settings</h3>
                    <p class="text-subtitle text-muted">Kelola admin dan monitor audit log sistem</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Settings</li>
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
                    {{-- SECTION: Tab Navigation untuk Settings --}}
                    {{-- Fungsi: Navigation untuk berpindah antar 2 tab (Manajemen Admin dan Audit Log) --}}
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" id="settingsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="manajemenadmin-tab" data-bs-toggle="tab"
                                data-bs-target="#manajemenadmin" type="button" role="tab" aria-controls="manajemenadmin"
                                aria-selected="true">
                                <i class="bi bi-people-fill me-2"></i>Manajemen Admin
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="auditlog-tab" data-bs-toggle="tab" data-bs-target="#auditlog"
                                type="button" role="tab" aria-controls="auditlog" aria-selected="false">
                                <i class="bi bi-shield-lock-fill me-2"></i>Audit Log
                            </button>
                        </li>
                    </ul>

                    {{-- SECTION: Tab Content Container --}}
                    {{-- Fungsi: Wrapper untuk semua konten tab --}}
                    <!-- Tab panes -->
                    <div class="tab-content" id="settingsTabContent">
                        {{-- TAB 1: Manajemen Admin --}}
                        {{-- Fungsi: Menampilkan daftar admin yang terdaftar dan tombol untuk tambah admin baru --}}
                        {{-- Isi: Tabel dengan kolom Foto, NIK, Nama, Email, Role, Status, Terakhir Login, dan Aksi --}}
                        <!-- Manajemen Admin Tab -->
                        <div class="tab-pane fade show active" id="manajemenadmin" role="tabpanel"
                            aria-labelledby="manajemenadmin-tab">
                            <div class="mt-4">
                                {{-- Header dengan tombol Tambah Admin --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Manajemen Admin</h5>
                                    <button type="button" class="btn btn-primary" id="btnTambahAdmin">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Admin
                                    </button>
                                </div>

                                {{-- Alert warning tentang keamanan akses admin --}}
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Perhatian!</strong> Hanya user dengan role admin yang dapat mengakses halaman ini. 
                                    Pastikan hanya memberikan akses kepada orang yang berwenang.
                                </div>

                                {{-- Tabel Daftar Admin --}}
                                {{-- Kolom: Foto, NIK, Nama, Email, Role Admin, Status, Terakhir Login, Aksi (Detail/Edit/Reset Password/Nonaktifkan) --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableAdmin">
                                        <thead>
                                            <tr>
                                                <th>Foto</th>
                                                <th>NIK</th>
                                                <th>Nama Lengkap</th>
                                                <th>Email</th>
                                                <th>Role Admin</th>
                                                <th>Status</th>
                                                <th>Terakhir Login</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                    </div>
                                                </td>
                                                <td><strong>2024001</strong></td>
                                                <td>
                                                    <strong>John Administrator</strong><br>
                                                    <small class="text-muted">IT Manager</small>
                                                </td>
                                                <td>john.admin@metinca.com</td>
                                                <td>
                                                    <span class="badge bg-danger" style="font-size: 0.9rem;">
                                                        <i class="bi bi-shield-fill-check"></i> Super Admin
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Active
                                                    </span>
                                                </td>
                                                <td>
                                                    17 Jan 2025<br>
                                                    <small class="text-muted">09:30 WIB</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" title="Reset Password">
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                    </div>
                                                </td>
                                                <td><strong>2024015</strong></td>
                                                <td>
                                                    <strong>Sarah Kusuma</strong><br>
                                                    <small class="text-muted">HR Manager</small>
                                                </td>
                                                <td>sarah.hr@metinca.com</td>
                                                <td>
                                                    <span class="badge bg-primary" style="font-size: 0.9rem;">
                                                        <i class="bi bi-person-badge"></i> Admin HR
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Active
                                                    </span>
                                                </td>
                                                <td>
                                                    17 Jan 2025<br>
                                                    <small class="text-muted">08:15 WIB</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary me-1" title="Reset Password">
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Nonaktifkan">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Avatar">
                                                    </div>
                                                </td>
                                                <td><strong>2024028</strong></td>
                                                <td>
                                                    <strong>Budi Prasetyo</strong><br>
                                                    <small class="text-muted">Document Controller</small>
                                                </td>
                                                <td>budi.dc@metinca.com</td>
                                                <td>
                                                    <span class="badge bg-info" style="font-size: 0.9rem;">
                                                        <i class="bi bi-file-earmark-text"></i> Admin Document Control
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Active
                                                    </span>
                                                </td>
                                                <td>
                                                    16 Jan 2025<br>
                                                    <small class="text-muted">16:45 WIB</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary me-1" title="Reset Password">
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Nonaktifkan">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Avatar">
                                                    </div>
                                                </td>
                                                <td><strong>2024042</strong></td>
                                                <td>
                                                    <strong>Dewi Anggraini</strong><br>
                                                    <small class="text-muted">Training Coordinator</small>
                                                </td>
                                                <td>dewi.training@metinca.com</td>
                                                <td>
                                                    <span class="badge bg-primary" style="font-size: 0.9rem;">
                                                        <i class="bi bi-person-badge"></i> Admin HR
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Active
                                                    </span>
                                                </td>
                                                <td>
                                                    17 Jan 2025<br>
                                                    <small class="text-muted">07:50 WIB</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary me-1" title="Reset Password">
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Nonaktifkan">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                    </div>
                                                </td>
                                                <td><strong>2024055</strong></td>
                                                <td>
                                                    <strong>Rudi Setiawan</strong><br>
                                                    <small class="text-muted">Former Admin</small>
                                                </td>
                                                <td>rudi.admin@metinca.com</td>
                                                <td>
                                                    <span class="badge bg-secondary" style="font-size: 0.9rem;">
                                                        <i class="bi bi-file-earmark-text"></i> Admin Document Control
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-x-circle-fill"></i> Inactive
                                                    </span>
                                                </td>
                                                <td>
                                                    10 Dec 2024<br>
                                                    <small class="text-muted">17:20 WIB</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success" title="Aktifkan Kembali">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Card Informasi: Deskripsi Role Admin --}}
                                {{-- Fungsi: Menjelaskan 3 role admin dan hak akses masing-masing --}}
                                {{-- Isi: 3 kolom dengan penjelasan Super Admin, Admin HR, Admin Document Control --}}
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Deskripsi Role Admin</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Role 1: Super Admin --}}
                                            {{-- Hak akses penuh ke sistem --}}
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start mb-3">
                                                    <span class="badge bg-danger me-3" style="font-size: 1.5rem;">
                                                        <i class="bi bi-shield-fill-check"></i>
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-1">Super Admin</h6>
                                                        <p class="text-muted small mb-0">
                                                            Akses penuh ke seluruh sistem. Dapat mengelola admin lain, 
                                                            melihat audit log, dan mengubah semua data.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Role 2: Admin HR --}}
                                            {{-- Hak akses untuk mengelola karyawan, training, dan evaluasi --}}
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start mb-3">
                                                    <span class="badge bg-primary me-3" style="font-size: 1.5rem;">
                                                        <i class="bi bi-person-badge"></i>
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-1">Admin HR</h6>
                                                        <p class="text-muted small mb-0">
                                                            Mengelola data karyawan, training, evaluasi ujian, 
                                                            dan cetak sertifikat. Tidak dapat mengubah admin lain.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start mb-3">
                                                    <span class="badge bg-info me-3" style="font-size: 1.5rem;">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </span>
                                                    <div>
                                                        <h6 class="mb-1">Admin Document Control</h6>
                                                        <p class="text-muted small mb-0">
                                                            Fokus pada manajemen dokumen SOP/WI, upload revisi, 
                                                            dan media library. Akses terbatas hanya di area dokumen.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: Audit Log --}}
                        {{-- Fungsi: Menampilkan log audit semua aktivitas admin untuk keamanan dan compliance --}}
                        {{-- Isi: Tabel dengan filter berdasarkan jenis aksi dan tanggal, tombol export Excel --}}
                        <!-- Audit Log Tab -->
                        <div class="tab-pane fade" id="auditlog" role="tabpanel" aria-labelledby="auditlog-tab">
                            <div class="mt-4">
                                {{-- Header dengan filter dan export button --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Audit Log Sistem</h5>
                                    <div>
                                        {{-- Filter Select: Jenis Aksi (Create/Update/Delete/Login/Logout) --}}
                                        <select class="form-select d-inline-block w-auto me-2" id="filterAction">
                                            <option value="">Semua Aksi</option>
                                            <option value="create">Create</option>
                                            <option value="update">Update</option>
                                            <option value="delete">Delete</option>
                                            <option value="login">Login</option>
                                            <option value="logout">Logout</option>
                                        </select>
                                        {{-- Filter Input: Tanggal audit log --}}
                                        <input type="date" class="form-control d-inline-block w-auto me-2" id="filterDate">
                                        {{-- Tombol Export Audit Log ke Excel (untuk compliance ISO) --}}
                                        <button type="button" class="btn btn-success" id="btnExportAuditLog">
                                            <i class="bi bi-file-excel me-1"></i>Export Excel
                                        </button>
                                    </div>
                                </div>

                                {{-- Alert info tentang audit log dan retention policy --}}
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Semua aktivitas admin tercatat di sini untuk keperluan keamanan dan audit. 
                                    Log disimpan selama 2 tahun sesuai kebijakan ISO 9001.
                                </div>

                                {{-- Tabel Audit Log --}}
                                {{-- Kolom: ID Log, Waktu, User Admin, Aksi, Module, Detail Data, IP Address, Status --}}
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="tableAuditLog">
                                        <thead>
                                            <tr>
                                                <th>ID Log</th>
                                                <th>Waktu</th>
                                                <th>User Admin</th>
                                                <th>Aksi</th>
                                                <th>Module</th>
                                                <th>Detail Data</th>
                                                <th>IP Address</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><code>LOG-2025-0145</code></td>
                                                <td>
                                                    17 Jan 2025<br>
                                                    <small class="text-muted">09:35:24</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Sarah Kusuma</strong><br>
                                                            <small class="text-muted">Admin HR</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-pencil-fill"></i> UPDATE
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-primary">Master Data</span>
                                                </td>
                                                <td>
                                                    <strong>Employee Data</strong><br>
                                                    <small class="text-muted">NIK: 2024001 - Ahmad Fauzi</small><br>
                                                    <small class="text-info">Changed: Shift dari Pagi → Siang</small>
                                                </td>
                                                <td>
                                                    <code>192.168.1.105</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Success</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><code>LOG-2025-0144</code></td>
                                                <td>
                                                    17 Jan 2025<br>
                                                    <small class="text-muted">09:20:15</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Budi Prasetyo</strong><br>
                                                            <small class="text-muted">Admin DC</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-trash-fill"></i> DELETE
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-warning">Documents</span>
                                                </td>
                                                <td>
                                                    <strong>SOP Document</strong><br>
                                                    <small class="text-muted">Doc: WI-QC-001 Rev. 2</small><br>
                                                    <small class="text-danger">Reason: Obsolete (Replaced by Rev. 3)</small>
                                                </td>
                                                <td>
                                                    <code>192.168.1.112</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Success</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><code>LOG-2025-0143</code></td>
                                                <td>
                                                    17 Jan 2025<br>
                                                    <small class="text-muted">08:45:33</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Dewi Anggraini</strong><br>
                                                            <small class="text-muted">Admin HR</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-plus-circle-fill"></i> CREATE
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-info">Training</span>
                                                </td>
                                                <td>
                                                    <strong>New Training Catalog</strong><br>
                                                    <small class="text-muted">ID: TRN-004</small><br>
                                                    <small class="text-success">Title: Advanced Quality Control</small>
                                                </td>
                                                <td>
                                                    <code>192.168.1.108</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Success</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><code>LOG-2025-0142</code></td>
                                                <td>
                                                    17 Jan 2025<br>
                                                    <small class="text-muted">08:15:10</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Sarah Kusuma</strong><br>
                                                            <small class="text-muted">Admin HR</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-box-arrow-in-right"></i> LOGIN
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-secondary">Auth</span>
                                                </td>
                                                <td>
                                                    <strong>Login Success</strong><br>
                                                    <small class="text-muted">User authenticated successfully</small>
                                                </td>
                                                <td>
                                                    <code>192.168.1.105</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Success</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><code>LOG-2025-0141</code></td>
                                                <td>
                                                    16 Jan 2025<br>
                                                    <small class="text-muted">17:30:45</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>John Administrator</strong><br>
                                                            <small class="text-muted">Super Admin</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-pencil-fill"></i> UPDATE
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-danger">Settings</span>
                                                </td>
                                                <td>
                                                    <strong>Admin Management</strong><br>
                                                    <small class="text-muted">User: Rudi Setiawan (2024055)</small><br>
                                                    <small class="text-warning">Changed: Status from Active → Inactive</small>
                                                </td>
                                                <td>
                                                    <code>192.168.1.100</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Success</span>
                                                </td>
                                            </tr>
                                            <tr class="table-danger">
                                                <td><code>LOG-2025-0140</code></td>
                                                <td>
                                                    16 Jan 2025<br>
                                                    <small class="text-muted">16:50:12</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Unknown User</strong><br>
                                                            <small class="text-muted">-</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-box-arrow-in-right"></i> LOGIN
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-secondary">Auth</span>
                                                </td>
                                                <td>
                                                    <strong>Login Failed</strong><br>
                                                    <small class="text-danger">Invalid credentials</small><br>
                                                    <small class="text-muted">Username: admin123</small>
                                                </td>
                                                <td>
                                                    <code>203.142.10.55</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger">Failed</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><code>LOG-2025-0139</code></td>
                                                <td>
                                                    16 Jan 2025<br>
                                                    <small class="text-muted">16:45:30</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Budi Prasetyo</strong><br>
                                                            <small class="text-muted">Admin DC</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-box-arrow-right"></i> LOGOUT
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-secondary">Auth</span>
                                                </td>
                                                <td>
                                                    <strong>Logout Success</strong><br>
                                                    <small class="text-muted">User logged out successfully</small>
                                                </td>
                                                <td>
                                                    <code>192.168.1.112</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Success</span>
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
        </section>
    </div>

@endsection

{{-- Untuk menggunakan js --}}
@push('scripts')
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Check for hash in URL and activate corresponding tab
            function activateTabFromHash() {
                var hash = window.location.hash;
                if (hash) {
                    var tabId = hash.substring(1);
                    var tabButton = document.getElementById(tabId + '-tab');
                    if (tabButton) {
                        var tab = new bootstrap.Tab(tabButton);
                        tab.show();
                    }
                }
            }

            activateTabFromHash();

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                var hash = $(e.target).attr('data-bs-target');
                history.pushState(null, null, hash);
            });

            $(window).on('hashchange', function() {
                activateTabFromHash();
            });

            // Initialize DataTables
            $('#tableAdmin').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[6, 'desc']] // Sort by last login
            });

            var tableAuditLog = $('#tableAuditLog').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[1, 'desc']] // Sort by time
            });

            // Filter handlers
            $('#filterAction').on('change', function() {
                var action = $(this).val();
                if (action) {
                    tableAuditLog.column(3).search(action, false, false).draw();
                } else {
                    tableAuditLog.column(3).search('').draw();
                }
            });

            $('#filterDate').on('change', function() {
                var date = $(this).val();
                if (date) {
                    tableAuditLog.column(1).search(date).draw();
                } else {
                    tableAuditLog.column(1).search('').draw();
                }
            });

            // Button handlers
            $('#btnTambahAdmin').on('click', function() {
                alert('Fitur Tambah Admin akan segera tersedia.\n\nForm akan mencakup:\n- NIK Karyawan\n- Email\n- Password\n- Role Admin (Super Admin/Admin HR/Admin DC)\n- Status Active/Inactive');
            });

            $('#btnExportAuditLog').on('click', function() {
                alert('Fitur Export Audit Log akan segera tersedia.\n\nFile Excel akan berisi:\n- Semua log aktivitas\n- Filter sesuai tanggal yang dipilih\n- Format untuk compliance ISO 9001');
            });
        });
    </script>
@endpush
