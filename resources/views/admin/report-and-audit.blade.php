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
            border: 1px solid var(--bs-border-color);
            padding: 0.5rem;
            text-align: center;
            min-width: 100px;
            white-space: nowrap;
        }
        .matrix-table th {
            background: var(--bs-primary);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .matrix-table tbody th {
            background: var(--bs-tertiary-bg);
            position: sticky;
            left: 0;
            z-index: 5;
            text-align: left;
            font-weight: 600;
        }

        /* Sel level kompetensi (token dari theme-overrides.css) */
        .lvl-cell {
            display: inline-block;
            min-width: 2.4rem;
            padding: .3rem .5rem;
            border-radius: .4rem;
            font-weight: 700;
            font-size: .78rem;
        }
        .lvl-cell.lvl-0 { background: var(--lvl-0-bg); color: var(--lvl-0-fg); }
        .lvl-cell.lvl-1 { background: var(--lvl-1-bg); color: var(--lvl-1-fg); }
        .lvl-cell.lvl-2 { background: var(--lvl-2-bg); color: var(--lvl-2-fg); }
        .lvl-cell.lvl-3 { background: var(--lvl-3-bg); color: var(--lvl-3-fg); }
        .lvl-cell.lvl-4 { background: var(--lvl-4-bg); color: var(--lvl-4-fg); }

        /* Kartu statistik per level */
        .lvl-stat {
            height: 100%;
            padding: .8rem 1rem;
            background: var(--bs-secondary-bg);
            border: 1px solid var(--bs-border-color);
            border-left-width: 4px;
            border-radius: .6rem;
        }
        .lvl-stat-value {
            font-size: 1.3rem;
            font-weight: 700;
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
        }
        .lvl-stat-label {
            font-size: .76rem;
            color: var(--bs-secondary-color);
        }
        .lvl-stat.s0 { border-left-color: var(--lvl-0-fg); }
        .lvl-stat.s1 { border-left-color: var(--lvl-1-fg); }
        .lvl-stat.s2 { border-left-color: var(--lvl-2-fg); }
        .lvl-stat.s3 { border-left-color: var(--lvl-3-fg); }
        .lvl-stat.s4 { border-left-color: var(--lvl-4-fg); }
        .lvl-stat.s0 .lvl-stat-value { color: var(--lvl-0-fg); }
        .lvl-stat.s1 .lvl-stat-value { color: var(--lvl-1-fg); }
        .lvl-stat.s2 .lvl-stat-value { color: var(--lvl-2-fg); }
        .lvl-stat.s3 .lvl-stat-value { color: var(--lvl-3-fg); }
        .lvl-stat.s4 .lvl-stat-value { color: var(--lvl-4-fg); }

        /* Panduan level */
        .matrix-guide {
            padding: .9rem 1rem;
            background: var(--bs-tertiary-bg);
            border-left: 4px solid var(--bs-primary);
            border-radius: .5rem;
        }
        .matrix-guide-line {
            font-size: .84rem;
            line-height: 2;
            color: var(--bs-secondary-color);
        }
        .lvl-tag {
            display: inline-block;
            min-width: 2rem;
            padding: .05rem .45rem;
            font-size: .72rem;
            font-weight: 700;
            text-align: center;
            border-radius: .35rem;
        }
        .lvl-tag.t0 { background: var(--lvl-0-bg); color: var(--lvl-0-fg); }
        .lvl-tag.t1 { background: var(--lvl-1-bg); color: var(--lvl-1-fg); }
        .lvl-tag.t2 { background: var(--lvl-2-bg); color: var(--lvl-2-fg); }
        .lvl-tag.t3 { background: var(--lvl-3-bg); color: var(--lvl-3-fg); }
        .lvl-tag.t4 { background: var(--lvl-4-bg); color: var(--lvl-4-fg); }

        /* Legend bawah tabel */
        .matrix-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: .82rem;
        }
        .legend-box {
            width: 30px;
            height: 20px;
            border-radius: 3px;
            border: 1px solid var(--bs-border-color);
        }
        .legend-box.b0 { background: var(--lvl-0-bg); }
        .legend-box.b1 { background: var(--lvl-1-bg); }
        .legend-box.b2 { background: var(--lvl-2-bg); }
        .legend-box.b3 { background: var(--lvl-3-bg); }
        .legend-box.b4 { background: var(--lvl-4-bg); }
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
                                {{-- Header dengan Filter Departemen dan Divisi --}}
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="filterDepartemen" class="form-label"><strong>Pilih Departemen:</strong></label>
                                        <select class="form-select" id="filterDepartemen">
                                            <option value="">-- Semua Departemen --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="filterDivisi" class="form-label"><strong>Pilih Divisi:</strong></label>
                                        <select class="form-select" id="filterDivisi">
                                            <option value="">-- Semua Divisi --</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Dashboard Stats --}}
                                <div class="row g-3 mb-4" id="dashboardStats">
                                    <div class="col-md-2 col-6">
                                        <div class="lvl-stat s0">
                                            <div class="lvl-stat-value level-0-count">0</div>
                                            <div class="lvl-stat-label">Level 0 &mdash; Belum Training</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6">
                                        <div class="lvl-stat s1">
                                            <div class="lvl-stat-value level-1-count">0</div>
                                            <div class="lvl-stat-label">Level 1 &mdash; Sedang Belajar</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6">
                                        <div class="lvl-stat s2">
                                            <div class="lvl-stat-value level-2-count">0</div>
                                            <div class="lvl-stat-label">Level 2 &mdash; Mulai Mandiri</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6">
                                        <div class="lvl-stat s3">
                                            <div class="lvl-stat-value level-3-count">0</div>
                                            <div class="lvl-stat-label">Level 3 &mdash; Mandiri Penuh</div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6">
                                        <div class="lvl-stat s4">
                                            <div class="lvl-stat-value level-4-count">0</div>
                                            <div class="lvl-stat-label">Level 4 &mdash; Expert/Instruktur</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Panduan Matriks Level --}}
                                <div class="matrix-guide mb-4">
                                    <strong>Panduan Level Kompetensi (Per Skill):</strong>
                                    <div class="matrix-guide-line mt-1">
                                        <span class="lvl-tag t0">L0</span> Belum melakukan training &nbsp;&bull;&nbsp; <span class="lvl-tag t1">L1</span> Sedang dalam proses pembelajaran<br>
                                        <span class="lvl-tag t2">L2</span> Mulai dapat dikerjakan dengan supervisi &nbsp;&bull;&nbsp; <span class="lvl-tag t3">L3</span> Dapat dikerjakan mandiri dengan baik<br>
                                        <span class="lvl-tag t4">L4</span> Mahir dan dapat mengajarkan ke karyawan lain
                                    </div>
                                </div>

                                {{-- Tabel Kompetensi Skill-Based --}}

                                <div class="table-responsive">
                                    <table class="table table-striped table-sm" id="skillMatrixTable">
                                        <thead id="skillTableHead">
                                            <tr>
                                                <th style="width: 50px; text-align: center;">No</th>
                                                <th style="width: 14%; text-align: left;">Nama & NIK</th>
                                                <th style="width: 12%; text-align: left;">Departemen</th>
                                                <th style="width: 12%; text-align: left;">Jabatan</th>
                                                <th style="width: 10%; text-align: center;">Status</th>
                                                <!-- Skill columns akan diisi dynamically -->
                                            </tr>
                                        </thead>
                                        <tbody id="skillTableBody">
                                            <tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>
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
                                                    <span class="badge bg-success">85%</span>
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
                                                    <span class="badge bg-success">90%</span>
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
                                                    <span class="badge bg-success">88%</span>
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
                                                    <span class="badge bg-success">95%</span>
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
                                                    <span class="badge bg-success">92%</span>
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
                                                    <span class="badge bg-success">92%</span>
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
        // Load department dropdown dynamically
        function loadDepartemenDynamic() {
            $.ajax({
                url: '/api/departments/list',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        const filterDept = document.getElementById('filterDepartemen');
                        filterDept.innerHTML = '<option value="">-- Semua Departemen --</option>';
                        
                        response.data.forEach(function(dept) {
                            const option = document.createElement('option');
                            option.value = dept.id;
                            option.textContent = dept.name;
                            filterDept.appendChild(option);
                        });
                    }
                }
            });
        }

        // Load divisions based on selected department
        function loadDivisiByDepartemen(departmentId) {
            const filterDiv = document.getElementById('filterDivisi');
            filterDiv.innerHTML = '<option value="">-- Semua Divisi --</option>';
            
            if (!departmentId) return;
            
            $.ajax({
                url: '/api/divisions?department_id=' + departmentId,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        response.data.forEach(function(div) {
                            const option = document.createElement('option');
                            option.value = div.id;
                            option.textContent = div.name;
                            filterDiv.appendChild(option);
                        });
                    }
                }
            });
        }

        // Load skill-based competency matrix
        function loadSkillMatrixData() {
            const deptId = document.getElementById('filterDepartemen').value;
            const divId = document.getElementById('filterDivisi').value;
            const tableBody = document.getElementById('skillTableBody');
            const tableHead = document.getElementById('skillTableHead');
            
            let url = '/api/competencies/skills';
            let params = [];
            if (deptId) params.push('department_id=' + deptId);
            if (divId) params.push('division_id=' + divId);
            if (params.length > 0) url += '?' + params.join('&');
            
            console.log('Loading skill matrix from:', url);
            
            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Skill matrix response:', response);
                    if (response.success) {
                        renderSkillMatrix(response.data, response.skills, tableHead, tableBody);
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error: ' + response.message + '</td></tr>';
                    }
                },
                error: function(xhr) {
                    console.error('Error loading skill matrix:', xhr);
                    tableBody.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Gagal memuat data: ' + xhr.status + '</td></tr>';
                }
            });
        }

        // Render skill-based matrix
        function renderSkillMatrix(employees, skills, tableHead, tableBody) {
            console.log('renderSkillMatrix called with:', {employees, skills});
            
            if (!employees || employees.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="' + (5 + skills.length) + '" class="text-center text-muted">Tidak ada data</td></tr>';
                updateSkillStats({});
                return;
            }

            // Update header dengan skill columns
            let headerHtml = `
                <th style="width: 50px; text-align: center;">No</th>
                <th style="width: 14%; text-align: left;">Nama & NIK</th>
                <th style="width: 12%; text-align: left;">Departemen</th>
                <th style="width: 12%; text-align: left;">Jabatan</th>
                <th style="width: 10%; text-align: center;">Status</th>
            `;
            
            skills.forEach(skill => {
                headerHtml += `<th style="width: 8%; text-align: center; font-size: 0.85rem;" data-skill-id="${skill.id}">${escapeHtml(skill.code)}</th>`;
            });
            
            tableHead.innerHTML = '<tr>' + headerHtml + '</tr>';

            // Calculate stats
            const stats = { 0: 0, 1: 0, 2: 0, 3: 0, 4: 0 };

            // Render rows
            let bodyHtml = '';
            employees.forEach((emp, index) => {
                const statusBadge = emp.status === 'active' 
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-warning text-dark">Non-Aktif</span>';

                let rowHtml = `
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                        <td style="text-align: left;">
                            <div class="fw-semibold">${escapeHtml(emp.nama)}</div>
                            <div style="font-size: 0.85rem;">NIK: ${escapeHtml(emp.nik)}</div>
                        </td>
                        <td style="text-align: left; font-size: 0.9rem;">${escapeHtml(emp.departemen)}</td>
                        <td style="text-align: left; font-size: 0.9rem;">${escapeHtml(emp.jabatan)}</td>
                        <td style="text-align: center;">${statusBadge}</td>
                `;

                // Add skill level cells
                skills.forEach(skill => {
                    const skillData = emp.skills[skill.id];
                    const level = skillData ? skillData.level : 0;
                    
                    stats[level]++;
                    
                    rowHtml += `
                        <td style="text-align: center; vertical-align: middle;">
                            <span class="lvl-cell lvl-${level}">L${level}</span>
                        </td>
                    `;
                });

                rowHtml += '</tr>';
                bodyHtml += rowHtml;
            });

            tableBody.innerHTML = bodyHtml;
            updateSkillStats(stats);
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Update dashboard stats
        function updateSkillStats(stats) {
            // Default to 0 for all levels if stats is empty
            stats = stats || {};
            document.querySelector('.level-0-count').textContent = stats[0] || 0;
            document.querySelector('.level-1-count').textContent = stats[1] || 0;
            document.querySelector('.level-2-count').textContent = stats[2] || 0;
            document.querySelector('.level-3-count').textContent = stats[3] || 0;
            document.querySelector('.level-4-count').textContent = stats[4] || 0;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDepartemenDynamic();
            loadSkillMatrixData();

            // Filter event listeners
            document.getElementById('filterDepartemen').addEventListener('change', function() {
                loadDivisiByDepartemen(this.value);
                loadSkillMatrixData();
            });

            document.getElementById('filterDivisi').addEventListener('change', function() {
                loadSkillMatrixData();
            });
        });
    </script>
@endpush