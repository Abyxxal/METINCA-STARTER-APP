{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Report & Audit')

{{-- Untuk menggunakan css --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
    <style>
        .competency-matrix {
            overflow-x: auto;
            max-width: 100%;
        }
        .matrix-table {
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .matrix-table th,
        .matrix-table td {
            border: 1px solid #dee2e6;
            padding: 0.5rem;
            text-align: center;
            min-width: 100px;
            white-space: nowrap;
        }
        .matrix-table th {
            background: #435ebe;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .matrix-table tbody th {
            background: #f8f9fa;
            position: sticky;
            left: 0;
            z-index: 5;
            text-align: left;
            font-weight: 600;
        }
        .cell-competent {
            background: #28a745;
            color: white;
            font-weight: bold;
        }
        .cell-level-3 {
            background: #ffc107;
            color: #333;
            font-weight: bold;
        }
        .cell-level-2 {
            background: #fd7e14;
            color: white;
            font-weight: bold;
        }
        .cell-level-1 {
            background: #0dcaf0;
            color: white;
            font-weight: bold;
        }
        .cell-not-passed {
            background: #dc3545;
            color: white;
            font-weight: bold;
        }
        .cell-not-trained {
            background: #6c757d;
            color: white;
        }
        .matrix-legend {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .legend-box {
            width: 30px;
            height: 20px;
            border-radius: 3px;
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
                    {{-- Nama: Report & Audit --}}
                    {{-- Fungsi: Reporting dan compliance untuk audit ISO 9001 dengan tracking kompetensi karyawan dan certificate management --}}
                    <h3>Report & Audit</h3>
                    <p class="text-subtitle text-muted">Matriks kompetensi, riwayat pelatihan, dan cetak sertifikat</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Report & Audit</li>
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
                    {{-- SECTION: Tab Navigation untuk Report & Audit --}}
                    {{-- Fungsi: Navigasi untuk memilih antara 3 laporan: Matriks Kompetensi, Riwayat Pelatihan, dan Cetak Sertifikat --}}
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" id="reportAuditTab" role="tablist">
                        {{-- TAB 1: Matriks Kompetensi --}}
                        {{-- Isi: Grid matrix dengan karyawan sebagai baris, skill/training sebagai kolom, warna indikator status (Competent/Not Passed/Not Trained) --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="matrikskompetensi-tab" data-bs-toggle="tab"
                                data-bs-target="#matrikskompetensi" type="button" role="tab" aria-controls="matrikskompetensi"
                                aria-selected="true">
                                <i class="bi bi-grid-3x3-gap-fill me-2"></i>Matriks Kompetensi
                            </button>
                        </li>
                        {{-- TAB 2: Riwayat Pelatihan --}}
                        {{-- Isi: Complete history pelatihan per karyawan dengan tanggal, nilai ujian, status kelulusan (LULUS/GAGAL), sertifikat status --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="riwayatpelatihan-tab" data-bs-toggle="tab" data-bs-target="#riwayatpelatihan"
                                type="button" role="tab" aria-controls="riwayatpelatihan" aria-selected="false">
                                <i class="bi bi-clock-history me-2"></i>Riwayat Pelatihan
                            </button>
                        </li>
                        {{-- TAB 3: Cetak Sertifikat --}}
                        {{-- Isi: Daftar sertifikat yang bisa di-generate dan dicetak untuk karyawan yang telah lulus training --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cetaksertifikat-tab" data-bs-toggle="tab" data-bs-target="#cetaksertifikat"
                                type="button" role="tab" aria-controls="cetaksertifikat" aria-selected="false">
                                <i class="bi bi-award-fill me-2"></i>Cetak Sertifikat
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="reportAuditTabContent">
                        {{-- TAB CONTENT 1: Matriks Kompetensi Tab --}}
                        {{-- Fungsi: Menampilkan competency matrix untuk ISO 9001 audit dengan color-coded status setiap karyawan per skill --}}
                        <!-- Matriks Kompetensi Tab -->
                        <div class="tab-pane fade show active" id="matrikskompetensi" role="tabpanel"
                            aria-labelledby="matrikskompetensi-tab">
                            <div class="mt-4">
                                {{-- Header dengan Filter Departemen --}}
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="filterDepartemen" class="form-label"><strong>Pilih Departemen:</strong></label>
                                        <select class="form-select" id="filterDepartemen">
                                            <option value="">-- Semua Departemen --</option>
                                            <option value="quality">Quality</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="ppc">PPC</option>
                                            <option value="production">Produksi & Dev Engineering</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Dashboard Stats --}}
                                <div class="row mb-4" id="dashboardStats">
                                    <div class="col-md-3 mb-3">
                                        <div style="background-color: #f8f9fa; border-left: 4px solid #dc3545; padding: 15px; border-radius: 4px;">
                                            <div style="font-size: 1.2rem; font-weight: 600; color: #333;" class="level-1-count">0</div>
                                            <small style="color: #666;">Level 1 (Perlu Training)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div style="background-color: #f8f9fa; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px;">
                                            <div style="font-size: 1.2rem; font-weight: 600; color: #333;" class="level-2-count">0</div>
                                            <small style="color: #666;">Level 2 (Mandiri)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div style="background-color: #f8f9fa; border-left: 4px solid #0dcaf0; padding: 15px; border-radius: 4px;">
                                            <div style="font-size: 1.2rem; font-weight: 600; color: #333;" class="level-3-count">0</div>
                                            <small style="color: #666;">Level 3 (Supervisor)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div style="background-color: #f8f9fa; border-left: 4px solid #198754; padding: 15px; border-radius: 4px;">
                                            <div style="font-size: 1.2rem; font-weight: 600; color: #333;" class="level-4-count">0</div>
                                            <small style="color: #666;">Level 4 (Expert/Manager)</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Panduan Matriks Level --}}
                                <div style="background-color: #f8f9fa; padding: 12px; border-left: 4px solid #3d7c2a; margin-bottom: 20px; border-radius: 4px;">
                                    <strong style="font-size: 0.95rem;">Panduan Matriks Level:</strong>
                                    <div style="font-size: 0.85rem; color: #666; margin-top: 8px; line-height: 1.6;">
                                        <div><span style="color: #dc3545;">●</span> <strong>L1:</strong> Masih perlu dibimbing | <span style="color: #ffc107;">●</span> <strong>L2:</strong> Mulai bisa dilepas | <span style="color: #0dcaf0;">●</span> <strong>L3:</strong> Bisa mengerjakan sendiri | <span style="color: #198754;">●</span> <strong>L4:</strong> Bisa mengajarkan</div>
                                    </div>
                                </div>

                                {{-- Tabel Kompetensi --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="competencyTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px; text-align: center;">No</th>
                                                <th style="width: 18%; text-align: left;">Nama & NIK</th>
                                                <th style="width: 13%; text-align: left;">Departemen</th>
                                                <th style="width: 16%; text-align: left;">Jabatan Saat Ini</th>
                                                <th style="width: 12%; text-align: center;">Level Kompetensi</th>
                                                <th style="width: 18%; text-align: left;">Wewenang / Status</th>
                                                <th style="width: 10%; text-align: center;">Status Karyawan</th>
                                                <th style="width: 9%; text-align: center;">History</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody" style="background-color: white;">
                                            <!-- Data akan dimuat via JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Riwayat Pelatihan Tab -->
                        {{-- TAB CONTENT 2: Riwayat Pelatihan Tab --}}
                        {{-- Fungsi: Menampilkan complete training history per karyawan dengan detail skor, status, dan sertifikat --}}
                        <div class="tab-pane fade" id="riwayatpelatihan" role="tabpanel" aria-labelledby="riwayatpelatihan-tab">
                            <div class="mt-4">
                                {{-- Header dengan search input dan export button --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Riwayat Pelatihan Karyawan</h5>
                                    {{-- Search untuk filter berdasarkan NIK atau nama karyawan --}}
                                    <div>
                                        <input type="text" class="form-control d-inline-block w-auto me-2" 
                                            placeholder="Cari NIK atau Nama..." id="searchKaryawan">
                                        <button type="button" class="btn btn-success" id="btnExportRiwayat">
                                            <i class="bi bi-file-excel me-1"></i>Export Excel
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabel Riwayat Pelatihan --}}
                                {{-- Kolom: NIK (employee ID), Nama Karyawan (employee name with avatar), Jabatan (position), Judul Training (training title), Kategori (category badge), Tanggal (training date), Skor (score %), Status (LULUS/GAGAL), Sertifikat (download button) --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableRiwayat">
                                        <thead>
                                            <tr>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Jabatan</th>
                                                <th>Judul Training</th>
                                                <th>Kategori</th>
                                                <th>Tanggal</th>
                                                <th>Skor</th>
                                                <th>Status</th>
                                                <th>Sertifikat</th>
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
                                                        <strong>Ahmad Fauzi</strong>
                                                    </div>
                                                </td>
                                                <td>Operator - Line A</td>
                                                <td>GMP Basic Training</td>
                                                <td><span class="badge bg-light-info">Safety</span></td>
                                                <td>15 Jan 2025</td>
                                                <td><strong class="text-success">85%</strong></td>
                                                <td><span class="badge bg-success">LULUS</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024001</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Ahmad Fauzi</strong>
                                                    </div>
                                                </td>
                                                <td>Operator - Line A</td>
                                                <td>5R Implementation</td>
                                                <td><span class="badge bg-light-warning">Quality</span></td>
                                                <td>10 Jan 2025</td>
                                                <td><strong class="text-success">90%</strong></td>
                                                <td><span class="badge bg-success">LULUS</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024001</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Ahmad Fauzi</strong>
                                                    </div>
                                                </td>
                                                <td>Operator - Line A</td>
                                                <td>Quality Control Basic</td>
                                                <td><span class="badge bg-light-primary">Quality</span></td>
                                                <td>20 Dec 2024</td>
                                                <td><strong class="text-danger">65%</strong></td>
                                                <td><span class="badge bg-danger">GAGAL</span></td>
                                                <td>
                                                    <span class="text-muted">-</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024002</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Siti Nurhaliza</strong>
                                                    </div>
                                                </td>
                                                <td>QC Inspector</td>
                                                <td>7 QC Tools</td>
                                                <td><span class="badge bg-light-primary">Quality</span></td>
                                                <td>18 Jan 2025</td>
                                                <td><strong class="text-success">88%</strong></td>
                                                <td><span class="badge bg-success">LULUS</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" title="Download">
                                                        <i class="bi bi-download"></i>
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
                                                        <strong>Siti Nurhaliza</strong>
                                                    </div>
                                                </td>
                                                <td>QC Inspector</td>
                                                <td>Quality Control Basic</td>
                                                <td><span class="badge bg-light-primary">Quality</span></td>
                                                <td>12 Jan 2025</td>
                                                <td><strong class="text-success">95%</strong></td>
                                                <td><span class="badge bg-success">LULUS</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024003</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Budi Santoso</strong>
                                                    </div>
                                                </td>
                                                <td>Technician</td>
                                                <td>Machine Maintenance</td>
                                                <td><span class="badge bg-light-danger">Technical</span></td>
                                                <td>16 Jan 2025</td>
                                                <td><strong class="text-success">92%</strong></td>
                                                <td><span class="badge bg-success">LULUS</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" title="Download">
                                                        <i class="bi bi-download"></i>
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
                                                        <strong>Dewi Lestari</strong>
                                                    </div>
                                                </td>
                                                <td>Line Leader</td>
                                                <td>Leadership & Communication</td>
                                                <td><span class="badge bg-light-success">Management</span></td>
                                                <td>14 Jan 2025</td>
                                                <td><strong class="text-success">92%</strong></td>
                                                <td><span class="badge bg-success">LULUS</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Cetak Sertifikat Tab -->
                        {{-- TAB CONTENT 3: Cetak Sertifikat Tab --}}
                        {{-- Fungsi: Management dan printing sertifikat pelatihan untuk karyawan yang telah lulus dengan tracking validity period --}}
                        <div class="tab-pane fade" id="cetaksertifikat" role="tabpanel" aria-labelledby="cetaksertifikat-tab">
                            <div class="mt-4">
                                {{-- Header dengan filter training dan cetak massal button --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Cetak Sertifikat Karyawan</h5>
                                    {{-- Filter untuk memilih jenis training tertentu (GMP, 5R, QC, Safety, Technical) --}}
                                    <div>
                                        <select class="form-select d-inline-block w-auto me-2" id="filterTraining">
                                            <option value="">Semua Training</option>
                                            <option value="gmp">GMP Basic</option>
                                            <option value="5r">5R Implementation</option>
                                            <option value="qc">Quality Control</option>
                                            <option value="safety">Safety & K3</option>
                                            <option value="technical">Technical Skill</option>
                                        </select>
                                        <button type="button" class="btn btn-primary" id="btnCetakMassalSertifikat">
                                            <i class="bi bi-printer me-1"></i>Cetak Massal
                                        </button>
                                    </div>
                                </div>

                                {{-- Information box tentang format sertifikat --}}
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Sertifikat akan di-generate dalam format PDF dengan template standar perusahaan. 
                                    Termasuk: Nama Peserta, Nilai Akhir, Tanggal Lulus, dan Tanda Tangan Digital Manager QA.
                                </div>

                                {{-- Tabel Sertifikat dengan checkbox untuk mass printing --}}
                                {{-- Kolom: Checkbox (untuk multi-select), NIK, Nama Karyawan (dengan avatar), Jabatan, Judul Training (dengan Cert ID), Tanggal Lulus, Nilai Akhir, Berlaku Sampai (validity period), Aksi (Print/Download/View) --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableSertifikat">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="selectAll">
                                                </th>
                                                <th>NIK</th>
                                                <th>Nama Karyawan</th>
                                                <th>Jabatan</th>
                                                <th>Judul Training</th>
                                                <th>Tanggal Lulus</th>
                                                <th>Nilai Akhir</th>
                                                <th>Berlaku Sampai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-item">
                                                </td>
                                                <td>2024001</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Ahmad Fauzi</strong>
                                                    </div>
                                                </td>
                                                <td>Operator - Line A</td>
                                                <td>
                                                    <strong>GMP Basic Training</strong><br>
                                                    <small class="text-muted">ID: CERT-GMP-2025-001</small>
                                                </td>
                                                <td>15 Jan 2025</td>
                                                <td>
                                                    <span class="badge bg-success" style="font-size: 1rem;">85%</span>
                                                </td>
                                                <td>
                                                    15 Jan 2027<br>
                                                    <small class="text-muted">(2 tahun)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" title="Download PDF">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn btn-sm btn-info" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-item">
                                                </td>
                                                <td>2024001</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Ahmad Fauzi</strong>
                                                    </div>
                                                </td>
                                                <td>Operator - Line A</td>
                                                <td>
                                                    <strong>5R Implementation</strong><br>
                                                    <small class="text-muted">ID: CERT-5R-2025-002</small>
                                                </td>
                                                <td>10 Jan 2025</td>
                                                <td>
                                                    <span class="badge bg-success" style="font-size: 1rem;">90%</span>
                                                </td>
                                                <td>
                                                    10 Jan 2027<br>
                                                    <small class="text-muted">(2 tahun)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" title="Download PDF">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn btn-sm btn-info" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-item">
                                                </td>
                                                <td>2024002</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Siti Nurhaliza</strong>
                                                    </div>
                                                </td>
                                                <td>QC Inspector</td>
                                                <td>
                                                    <strong>7 QC Tools</strong><br>
                                                    <small class="text-muted">ID: CERT-QC-2025-003</small>
                                                </td>
                                                <td>18 Jan 2025</td>
                                                <td>
                                                    <span class="badge bg-success" style="font-size: 1rem;">88%</span>
                                                </td>
                                                <td>
                                                    18 Jan 2027<br>
                                                    <small class="text-muted">(2 tahun)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" title="Download PDF">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn btn-sm btn-info" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-item">
                                                </td>
                                                <td>2024002</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Siti Nurhaliza</strong>
                                                    </div>
                                                </td>
                                                <td>QC Inspector</td>
                                                <td>
                                                    <strong>Quality Control Basic</strong><br>
                                                    <small class="text-muted">ID: CERT-QCB-2025-004</small>
                                                </td>
                                                <td>12 Jan 2025</td>
                                                <td>
                                                    <span class="badge bg-success" style="font-size: 1rem;">95%</span>
                                                </td>
                                                <td>
                                                    12 Jan 2027<br>
                                                    <small class="text-muted">(2 tahun)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" title="Download PDF">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn btn-sm btn-info" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-item">
                                                </td>
                                                <td>2024003</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Budi Santoso</strong>
                                                    </div>
                                                </td>
                                                <td>Technician</td>
                                                <td>
                                                    <strong>Machine Maintenance</strong><br>
                                                    <small class="text-muted">ID: CERT-MTN-2025-005</small>
                                                </td>
                                                <td>16 Jan 2025</td>
                                                <td>
                                                    <span class="badge bg-success" style="font-size: 1rem;">92%</span>
                                                </td>
                                                <td>
                                                    16 Jan 2027<br>
                                                    <small class="text-muted">(2 tahun)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" title="Download PDF">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn btn-sm btn-info" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-item">
                                                </td>
                                                <td>2024004</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Dewi Lestari</strong>
                                                    </div>
                                                </td>
                                                <td>Line Leader</td>
                                                <td>
                                                    <strong>Leadership & Communication</strong><br>
                                                    <small class="text-muted">ID: CERT-LDR-2025-006</small>
                                                </td>
                                                <td>14 Jan 2025</td>
                                                <td>
                                                    <span class="badge bg-success" style="font-size: 1rem;">92%</span>
                                                </td>
                                                <td>
                                                    14 Jan 2027<br>
                                                    <small class="text-muted">(2 tahun)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" title="Download PDF">
                                                        <i class="bi bi-file-pdf"></i> PDF
                                                    </button>
                                                    <button class="btn btn-sm btn-info" title="Preview">
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
        // Competency level descriptions and colors
        const levelDescriptions = {
            1: 'Masih perlu dibimbing',
            2: 'Mulai bisa dilepas',
            3: 'Bisa mengerjakan sendiri dgn baik',
            4: 'Bisa mengajarkan ke level rendah'
        };

        const levelColors = {
            1: '#dc3545',
            2: '#ffc107',
            3: '#0dcaf0',
            4: '#198754'
        };

        // Department mapping for API filter (will be populated dynamically)
        let departemenMap = {
            '': null
        };

        // Load department dropdown dynamically
        function loadDepartemenFilterDynamis() {
            const filterDepartemen = document.getElementById('filterDepartemen');
            
            $.ajax({
                url: '/api/departments/list',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        // Clear existing options except first
                        filterDepartemen.innerHTML = '<option value="">-- Semua Departemen --</option>';
                        
                        // Rebuild departemenMap
                        departemenMap = { '': null };
                        
                        // Add departments from API
                        response.data.forEach(function(dept) {
                            const option = document.createElement('option');
                            option.value = dept.id;
                            option.textContent = dept.name;
                            filterDepartemen.appendChild(option);
                            
                            // Map department ID for API filter
                            departemenMap[dept.id] = dept.id;
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading departments:', xhr);
                }
            });
        }

        // Initialize Competency Matrix
        function initCompetencyMatrix() {
            const filterDepartemen = document.getElementById('filterDepartemen');
            const tableBody = document.getElementById('tableBody');

            // Load initial data
            loadCompetencyDataFromAPI('', tableBody);

            // Filter on dropdown change
            filterDepartemen.addEventListener('change', function() {
                loadCompetencyDataFromAPI(this.value, tableBody);
            });
        }

        // Load competency data from API
        function loadCompetencyDataFromAPI(departemenFilter, tableBody) {
            const departmentId = departemenMap[departemenFilter] || null;
            let url = '/api/competencies';
            
            if (departmentId) {
                url += '?department_id=' + departmentId;
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    renderCompetencyTable(result.data, tableBody);
                } else {
                    console.error('Failed to load competencies:', result.message);
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Gagal memuat data</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching competencies:', error);
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error: ' + error.message + '</td></tr>';
            });
        }

        // Render competency table
        function renderCompetencyTable(data, tableBody) {
            tableBody.innerHTML = '';

            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Tidak ada data karyawan</td></tr>';
                updateCompetencyStats({1: 0, 2: 0, 3: 0, 4: 0});
                return;
            }

            // Calculate stats
            const stats = { 1: 0, 2: 0, 3: 0, 4: 0 };

            // Render rows - renumber based on filtered data
            data.forEach((item, index) => {
                const level = item.level || 1;
                stats[level]++;

                const badgeColor = levelColors[level];
                const wewenang = levelDescriptions[level];
                
                // Determine status badge
                const statusBadge = item.status === 'active' 
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-warning text-dark">Non-Aktif</span>';

                const row = `
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                        <td style="text-align: left;">
                            <div style="font-weight: 600; color: #333;">${item.nama}</div>
                            <div style="font-size: 0.85rem; color: #666;">NIK: ${item.nik}</div>
                        </td>
                        <td style="text-align: left; font-size: 0.9rem;">${item.departemen}</td>
                        <td style="text-align: left; font-size: 0.95rem;">${item.jabatan}</td>
                        <td style="text-align: center;">
                            <span style="display: inline-block; padding: 8px 16px; background-color: ${badgeColor}; color: white; border-radius: 6px; font-weight: 600; font-size: 0.95rem;">
                                Level ${level}
                            </span>
                        </td>
                        <td style="text-align: left; font-size: 0.9rem; color: #555; line-height: 1.4;">
                            ${wewenang}
                        </td>
                        <td style="text-align: center;">
                            ${statusBadge}
                        </td>
                        <td style="text-align: center;">
                            <button class="btn btn-sm" style="background-color: #3d7c2a; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;" onclick="showCompetencyHistory('${item.nik}', '${item.nama}', ${level}, '${wewenang}')">
                                <i class="bi bi-clock-history"></i> History
                            </button>
                        </td>
                    </tr>
                `;

                tableBody.innerHTML += row;
            });

            // Update stats
            updateCompetencyStats(stats);
        }

        // Update competency dashboard stats
        function updateCompetencyStats(stats) {
            document.querySelector('.level-1-count').textContent = stats[1] || 0;
            document.querySelector('.level-2-count').textContent = stats[2] || 0;
            document.querySelector('.level-3-count').textContent = stats[3] || 0;
            document.querySelector('.level-4-count').textContent = stats[4] || 0;
        }

        // Show competency history (demo for now)
        function showCompetencyHistory(nik, nama, level, wewenang) {
            const historyData = [
                { tanggal: '2025-01-15', level: level, catatan: 'Evaluasi terbaru' },
                { tanggal: '2024-12-01', level: Math.max(1, level - 1), catatan: 'Promosi' }
            ];

            alert(`Riwayat Kompetensi ${nama} (NIK: ${nik}):\n\n${historyData.map(h => `Level ${h.level} - ${h.catatan} (${h.tanggal})`).join('\n')}\n\nWewenang: ${wewenang}`);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDepartemenFilterDynamis();
            initCompetencyMatrix();
        });
    </script>
@endpush