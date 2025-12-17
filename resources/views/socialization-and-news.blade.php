{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Socialization & News')

{{-- Untuk menggunakan css --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
    <style>
        .announcement-card {
            border-left: 4px solid #435ebe;
            transition: all 0.3s;
        }
        .announcement-card.critical {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .announcement-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .read-status {
            font-size: 0.75rem;
        }
    </style>
@endpush

{{-- Isi content --}}
@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Socialization & News</h3>
                    <p class="text-subtitle text-muted">Kelola pengumuman dan monitor status baca karyawan</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Socialization & News</li>
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
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" id="socializationNewsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pengumuman-tab" data-bs-toggle="tab"
                                data-bs-target="#pengumuman" type="button" role="tab" aria-controls="pengumuman"
                                aria-selected="true">
                                <i class="bi bi-megaphone-fill me-2"></i>Buat Pengumuman
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="statusbaca-tab" data-bs-toggle="tab" data-bs-target="#statusbaca"
                                type="button" role="tab" aria-controls="statusbaca" aria-selected="false">
                                <i class="bi bi-check2-square me-2"></i>Status Baca
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="socializationNewsTabContent">
                        <!-- Buat Pengumuman Tab -->
                        <div class="tab-pane fade show active" id="pengumuman" role="tabpanel"
                            aria-labelledby="pengumuman-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Daftar Pengumuman</h5>
                                    <button type="button" class="btn btn-primary" id="btnTambahPengumuman">
                                        <i class="bi bi-plus-circle me-1"></i>Buat Pengumuman Baru
                                    </button>
                                </div>

                                <div class="row">
                                    <!-- Announcement Card 1 - Critical -->
                                    <div class="col-12 mb-3">
                                        <div class="card announcement-card critical">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-danger me-2">
                                                                <i class="bi bi-exclamation-triangle-fill"></i> CRITICAL
                                                            </span>
                                                            <span class="badge bg-light-secondary">
                                                                <i class="bi bi-eye-fill"></i> Force Read
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title mb-2">
                                                            <i class="bi bi-megaphone text-danger"></i>
                                                            Perubahan Shift Darurat - Area Produksi Line A
                                                        </h5>
                                                        <p class="card-text text-muted mb-2">
                                                            Informasi penting mengenai perubahan jadwal shift untuk karyawan Line A mulai tanggal 18 Januari 2025. 
                                                            Harap segera membaca dan konfirmasi...
                                                        </p>
                                                        <div class="d-flex align-items-center text-muted small">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            <span class="me-3">16 Jan 2025, 08:30</span>
                                                            <i class="bi bi-person-circle me-1"></i>
                                                            <span class="me-3">Admin HRD</span>
                                                            <i class="bi bi-paperclip me-1"></i>
                                                            <span class="me-3">2 Lampiran</span>
                                                            <i class="bi bi-check-circle text-success me-1"></i>
                                                            <span>45/120 Sudah Baca</span>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <button class="btn btn-sm btn-info mb-1" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-warning mb-1" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-success mb-1" title="Status Baca">
                                                            <i class="bi bi-people"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Announcement Card 2 - Normal -->
                                    <div class="col-12 mb-3">
                                        <div class="card announcement-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-primary me-2">
                                                                <i class="bi bi-info-circle-fill"></i> NORMAL
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title mb-2">
                                                            <i class="bi bi-calendar-event text-primary"></i>
                                                            Jadwal Training GMP Batch 3
                                                        </h5>
                                                        <p class="card-text text-muted mb-2">
                                                            Training GMP untuk karyawan baru batch 3 akan dilaksanakan pada tanggal 22-23 Januari 2025 di Ruang Meeting Lt.2. 
                                                            Peserta yang terdaftar harap hadir tepat waktu...
                                                        </p>
                                                        <div class="d-flex align-items-center text-muted small">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            <span class="me-3">15 Jan 2025, 14:00</span>
                                                            <i class="bi bi-person-circle me-1"></i>
                                                            <span class="me-3">Training Dept</span>
                                                            <i class="bi bi-paperclip me-1"></i>
                                                            <span class="me-3">1 Lampiran</span>
                                                            <i class="bi bi-check-circle text-success me-1"></i>
                                                            <span>25/30 Sudah Baca</span>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <button class="btn btn-sm btn-info mb-1" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-warning mb-1" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-success mb-1" title="Status Baca">
                                                            <i class="bi bi-people"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Announcement Card 3 - Normal -->
                                    <div class="col-12 mb-3">
                                        <div class="card announcement-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-primary me-2">
                                                                <i class="bi bi-info-circle-fill"></i> NORMAL
                                                            </span>
                                                            <span class="badge bg-light-secondary">
                                                                <i class="bi bi-eye-fill"></i> Force Read
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title mb-2">
                                                            <i class="bi bi-shield-check text-primary"></i>
                                                            Update Protokol Keselamatan Kerja
                                                        </h5>
                                                        <p class="card-text text-muted mb-2">
                                                            Terdapat update pada prosedur keselamatan kerja terbaru yang wajib diketahui oleh seluruh karyawan. 
                                                            Perubahan meliputi penggunaan APD baru dan prosedur lockout/tagout...
                                                        </p>
                                                        <div class="d-flex align-items-center text-muted small">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            <span class="me-3">14 Jan 2025, 10:15</span>
                                                            <i class="bi bi-person-circle me-1"></i>
                                                            <span class="me-3">Safety Officer</span>
                                                            <i class="bi bi-paperclip me-1"></i>
                                                            <span class="me-3">3 Lampiran</span>
                                                            <i class="bi bi-check-circle text-success me-1"></i>
                                                            <span>180/180 Sudah Baca</span>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <button class="btn btn-sm btn-info mb-1" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-warning mb-1" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-success mb-1" title="Status Baca">
                                                            <i class="bi bi-people"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Announcement Card 4 - Critical -->
                                    <div class="col-12 mb-3">
                                        <div class="card announcement-card critical">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="badge bg-danger me-2">
                                                                <i class="bi bi-exclamation-triangle-fill"></i> CRITICAL
                                                            </span>
                                                            <span class="badge bg-light-secondary">
                                                                <i class="bi bi-eye-fill"></i> Force Read
                                                            </span>
                                                        </div>
                                                        <h5 class="card-title mb-2">
                                                            <i class="bi bi-lightning-charge text-danger"></i>
                                                            Pemberitahuan Pemadaman Listrik Terjadwal
                                                        </h5>
                                                        <p class="card-text text-muted mb-2">
                                                            Akan dilakukan pemadaman listrik untuk maintenance transformer pada tanggal 20 Januari 2025 pukul 18:00-22:00 WIB. 
                                                            Semua operasional produksi akan dihentikan sementara...
                                                        </p>
                                                        <div class="d-flex align-items-center text-muted small">
                                                            <i class="bi bi-calendar3 me-1"></i>
                                                            <span class="me-3">13 Jan 2025, 16:45</span>
                                                            <i class="bi bi-person-circle me-1"></i>
                                                            <span class="me-3">Facility Manager</span>
                                                            <i class="bi bi-paperclip me-1"></i>
                                                            <span class="me-3">1 Lampiran</span>
                                                            <i class="bi bi-check-circle text-success me-1"></i>
                                                            <span>95/180 Sudah Baca</span>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <button class="btn btn-sm btn-info mb-1" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-warning mb-1" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-success mb-1" title="Status Baca">
                                                            <i class="bi bi-people"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Baca Tab -->
                        <div class="tab-pane fade" id="statusbaca" role="tabpanel" aria-labelledby="statusbaca-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Status Baca Pengumuman</h5>
                                    <div>
                                        <select class="form-select d-inline-block w-auto me-2" id="filterPengumuman">
                                            <option value="">Semua Pengumuman</option>
                                            <option value="shift">Perubahan Shift Darurat</option>
                                            <option value="training">Jadwal Training GMP</option>
                                            <option value="safety">Update Protokol Keselamatan</option>
                                            <option value="listrik">Pemadaman Listrik</option>
                                        </select>
                                        <select class="form-select d-inline-block w-auto me-2" id="filterStatus">
                                            <option value="">Semua Status</option>
                                            <option value="confirmed">Sudah Konfirmasi</option>
                                            <option value="pending">Belum Konfirmasi</option>
                                        </select>
                                        <button type="button" class="btn btn-success" id="btnExportStatusBaca">
                                            <i class="bi bi-file-excel me-1"></i>Export Excel
                                        </button>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Tabel ini menunjukkan audit trail pembacaan pengumuman oleh karyawan. 
                                    Status "Sudah Konfirmasi" menandakan user telah scroll sampai bawah dan klik tombol konfirmasi.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableStatusBaca">
                                        <thead>
                                            <tr>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Judul Pengumuman</th>
                                                <th>Waktu Membuka</th>
                                                <th>Waktu Konfirmasi</th>
                                                <th>Durasi Baca</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2024001</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Ahmad Fauzi</strong><br>
                                                            <small class="text-muted">Operator - Line A</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger mb-1">CRITICAL</span><br>
                                                    <small>Perubahan Shift Darurat - Area Produksi Line A</small>
                                                </td>
                                                <td>
                                                    16 Jan 2025<br>
                                                    <small class="text-muted">09:15 WIB</small>
                                                </td>
                                                <td>
                                                    16 Jan 2025<br>
                                                    <small class="text-muted">09:18 WIB</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">3 menit</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Sudah Konfirmasi
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024002</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Siti Nurhaliza</strong><br>
                                                            <small class="text-muted">QC Inspector</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary mb-1">NORMAL</span><br>
                                                    <small>Jadwal Training GMP Batch 3</small>
                                                </td>
                                                <td>
                                                    15 Jan 2025<br>
                                                    <small class="text-muted">14:30 WIB</small>
                                                </td>
                                                <td>
                                                    15 Jan 2025<br>
                                                    <small class="text-muted">14:32 WIB</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">2 menit</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Sudah Konfirmasi
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <td>2024003</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Budi Santoso</strong><br>
                                                            <small class="text-muted">Operator - Line A</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger mb-1">CRITICAL</span><br>
                                                    <small>Perubahan Shift Darurat - Area Produksi Line A</small>
                                                </td>
                                                <td>
                                                    16 Jan 2025<br>
                                                    <small class="text-muted">10:05 WIB</small>
                                                </td>
                                                <td>
                                                    <span class="text-muted">-</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-warning">Masih Dibuka</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-hourglass-split"></i> Belum Konfirmasi
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" title="Kirim Reminder">
                                                        <i class="bi bi-bell"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024004</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Dewi Lestari</strong><br>
                                                            <small class="text-muted">Safety Officer</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary mb-1">NORMAL</span><br>
                                                    <small>Update Protokol Keselamatan Kerja</small>
                                                </td>
                                                <td>
                                                    14 Jan 2025<br>
                                                    <small class="text-muted">10:30 WIB</small>
                                                </td>
                                                <td>
                                                    14 Jan 2025<br>
                                                    <small class="text-muted">10:35 WIB</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">5 menit</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Sudah Konfirmasi
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="table-danger">
                                                <td>2024005</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Rudi Hermawan</strong><br>
                                                            <small class="text-muted">Operator - Line A</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger mb-1">CRITICAL</span><br>
                                                    <small>Perubahan Shift Darurat - Area Produksi Line A</small>
                                                </td>
                                                <td>
                                                    <span class="text-muted">Belum Dibuka</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">-</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-danger">-</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle-fill"></i> Belum Dibaca
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger" title="Kirim Reminder Urgent">
                                                        <i class="bi bi-bell-fill"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024006</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <div>
                                                            <strong>Rina Agustina</strong><br>
                                                            <small class="text-muted">Technician</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger mb-1">CRITICAL</span><br>
                                                    <small>Pemberitahuan Pemadaman Listrik Terjadwal</small>
                                                </td>
                                                <td>
                                                    13 Jan 2025<br>
                                                    <small class="text-muted">17:00 WIB</small>
                                                </td>
                                                <td>
                                                    13 Jan 2025<br>
                                                    <small class="text-muted">17:04 WIB</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">4 menit</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> Sudah Konfirmasi
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
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
            $('#tableStatusBaca').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[3, 'desc']] // Sort by date opened
            });

            // Filter handlers
            $('#filterPengumuman').on('change', function() {
                var pengumuman = $(this).val();
                if (pengumuman) {
                    $('#tableStatusBaca').DataTable().column(2).search(pengumuman).draw();
                } else {
                    $('#tableStatusBaca').DataTable().column(2).search('').draw();
                }
            });

            $('#filterStatus').on('change', function() {
                var status = $(this).val();
                if (status === 'confirmed') {
                    $('#tableStatusBaca').DataTable().column(6).search('Sudah Konfirmasi').draw();
                } else if (status === 'pending') {
                    $('#tableStatusBaca').DataTable().column(6).search('Belum').draw();
                } else {
                    $('#tableStatusBaca').DataTable().column(6).search('').draw();
                }
            });

            // Button handlers
            $('#btnTambahPengumuman').on('click', function() {
                alert('Fitur Buat Pengumuman Baru akan segera tersedia.\n\nForm akan mencakup:\n- Judul Pengumuman\n- Isi Berita (WYSIWYG Editor)\n- Upload Lampiran File\n- Tingkat Urgensi (Normal/Critical)\n- Force Read Option');
            });

            $('#btnExportStatusBaca').on('click', function() {
                alert('Fitur Export Excel Status Baca akan segera tersedia');
            });
        });
    </script>
@endpush
