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
                                {{-- Header dengan filter departemen dan export button untuk ISO audit --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Matriks Kompetensi Karyawan</h5>
                                    {{-- Filter untuk departemen (Produksi, QC, Maintenance, Warehouse) --}}
                                    <div>
                                        <select class="form-select d-inline-block w-auto me-2" id="filterDepartemen">
                                            <option value="">Semua Departemen</option>
                                            <option value="produksi">Produksi</option>
                                            <option value="qc">Quality Control</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="warehouse">Warehouse</option>
                                        </select>
                                        <button type="button" class="btn btn-success" id="btnExportMatriks">
                                            <i class="bi bi-file-excel me-1"></i>Export Excel (ISO Audit)
                                        </button>
                                    </div>
                                </div>

                                {{-- Information box untuk legend --}}
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <strong>Tingkat Kemahiran Karyawan:</strong> Level mencerminkan kemampuan dan kemandirian karyawan dalam mengerjakan skill/training tertentu.
                                </div>

                                <div class="matrix-legend">
                                    <div class="legend-item">
                                        <div class="legend-box cell-level-1"></div>
                                        <span><strong>Level 1</strong> - Novice (Masih perlu dibimbing)</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-box cell-level-2"></div>
                                        <span><strong>Level 2</strong> - Beginner (Mulai bisa dilepas)</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-box cell-level-3"></div>
                                        <span><strong>Level 3</strong> - Proficient (Bisa mengerjakan sendiri dgn baik)</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-box cell-competent"></div>
                                        <span><strong>Level 4</strong> - Expert/Master (Bisa mengajarkan ke level rendah)</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-box cell-not-trained"></div>
                                        <span><strong>-</strong> - Belum Training</span>
                                    </div>
                                </div>

                                <div class="competency-matrix">
                                    <table class="matrix-table table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">NIK / Nama Karyawan</th>
                                                <th colspan="3">Safety & K3</th>
                                                <th colspan="2">Quality</th>
                                                <th colspan="2">Technical</th>
                                                <th colspan="1">Production</th>
                                            </tr>
                                            <tr>
                                                <th>GMP Basic</th>
                                                <th>5R/5S</th>
                                                <th>K3L</th>
                                                <th>QC Basic</th>
                                                <th>7 QC Tools</th>
                                                <th>Machine Operation</th>
                                                <th>Maintenance</th>
                                                <th>Line Leader</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>
                                                    2024001<br>
                                                    <small class="text-muted">Ahmad Fauzi</small><br>
                                                    <small class="badge bg-light-primary">Operator</small>
                                                </th>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-passed">
                                                    <i class="bi bi-x-circle-fill"></i><br><strong>Level 2</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    2024002<br>
                                                    <small class="text-muted">Siti Nurhaliza</small><br>
                                                    <small class="badge bg-light-warning">QC Inspector</small>
                                                </th>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    2024003<br>
                                                    <small class="text-muted">Budi Santoso</small><br>
                                                    <small class="badge bg-light-danger">Technician</small>
                                                </th>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    2024004<br>
                                                    <small class="text-muted">Dewi Lestari</small><br>
                                                    <small class="badge bg-light-success">Line Leader</small>
                                                </th>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    2024005<br>
                                                    <small class="text-muted">Rudi Hermawan</small><br>
                                                    <small class="badge bg-light-primary">Operator</small>
                                                </th>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-passed">
                                                    <i class="bi bi-x-circle-fill"></i><br><strong>Level 2</strong>
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br><strong>Level 4</strong>
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                            </tr>
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
            var tableRiwayat = $('#tableRiwayat').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[5, 'desc']] // Sort by date
            });

            $('#tableSertifikat').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[5, 'desc']] // Sort by date
            });

            // Search functionality
            $('#searchKaryawan').on('keyup', function() {
                tableRiwayat.search(this.value).draw();
            });

            // Data skill requirement per departemen
            const departemenSkills = {
                '': {
                    categoryHeaders: [
                        { label: 'Safety & K3', colspan: 3 },
                        { label: 'Quality', colspan: 2 },
                        { label: 'Technical', colspan: 2 },
                        { label: 'Production', colspan: 1 }
                    ],
                    skills: ['GMP Basic', '5R/5S', 'K3L', 'QC Basic', '7 QC Tools', 'Machine Operation', 'Maintenance', 'Line Leader'],
                    columnIndices: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                'produksi': {
                    categoryHeaders: [
                        { label: 'Safety & K3', colspan: 3 },
                        { label: 'Production', colspan: 2 }
                    ],
                    skills: ['GMP Basic', '5R/5S', 'K3L', 'Machine Operation', 'Line Leader'],
                    columnIndices: [0, 1, 2, 5, 7]
                },
                'qc': {
                    categoryHeaders: [
                        { label: 'Safety & K3', colspan: 2 },
                        { label: 'Quality', colspan: 2 }
                    ],
                    skills: ['GMP Basic', 'K3L', 'QC Basic', '7 QC Tools'],
                    columnIndices: [0, 2, 3, 4]
                },
                'maintenance': {
                    categoryHeaders: [
                        { label: 'Safety & K3', colspan: 2 },
                        { label: 'Technical', colspan: 2 }
                    ],
                    skills: ['GMP Basic', 'K3L', 'Machine Operation', 'Maintenance'],
                    columnIndices: [0, 2, 5, 6]
                },
                'warehouse': {
                    categoryHeaders: [
                        { label: 'Safety & K3', colspan: 3 }
                    ],
                    skills: ['GMP Basic', '5R/5S', 'K3L'],
                    columnIndices: [0, 1, 2]
                }
            };

            // Filter departemen handler
            $('#filterDepartemen').on('change', function() {
                var dept = $(this).val();
                var skillsData = departemenSkills[dept];
                
                if (!skillsData) return;
                
                var $table = $('.matrix-table');
                var $thead = $table.find('thead');
                var $tbody = $table.find('tbody');
                
                // Update header baris 1 (category)
                var row1 = '<tr><th rowspan="2" style="position: sticky; left: 0; z-index: 5;">NIK / Nama Karyawan</th>';
                skillsData.categoryHeaders.forEach(function(cat) {
                    row1 += '<th colspan="' + cat.colspan + '">' + cat.label + '</th>';
                });
                row1 += '</tr>';
                
                // Update header baris 2 (skill names)
                var row2 = '<tr>';
                skillsData.skills.forEach(function(skill) {
                    row2 += '<th>' + skill + '</th>';
                });
                row2 += '</tr>';
                
                $thead.html(row1 + row2);
                
                // Update tbody - hide/show columns
                $tbody.find('tr').each(function() {
                    var $cells = $(this).find('td, th');
                    $cells.each(function(index) {
                        // Header kolom (NIK/Nama) selalu ditampilkan
                        if (index === 0) {
                            $(this).show();
                        } else if (skillsData.columnIndices.includes(index - 1)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });
            });

            $('#filterTraining').on('change', function() {
                var training = $(this).val();
                if (training) {
                    $('#tableSertifikat').DataTable().column(4).search(training).draw();
                } else {
                    $('#tableSertifikat').DataTable().column(4).search('').draw();
                }
            });

            // Select All checkbox
            $('#selectAll').on('change', function() {
                $('.select-item').prop('checked', $(this).prop('checked'));
            });

            // Button handlers
            $('#btnExportMatriks').on('click', function() {
                alert('Fitur Export Excel Matriks Kompetensi akan segera tersedia.\n\nFile Excel akan berisi:\n- Matriks kompetensi lengkap\n- Color coding sesuai status\n- Format siap untuk audit ISO 9001');
            });

            $('#btnExportRiwayat').on('click', function() {
                alert('Fitur Export Excel Riwayat Pelatihan akan segera tersedia');
            });

            $('#btnCetakMassalSertifikat').on('click', function() {
                var selected = $('.select-item:checked').length;
                if (selected === 0) {
                    alert('Silakan pilih sertifikat yang akan dicetak');
                } else {
                    alert('Cetak ' + selected + ' sertifikat terpilih.\n\nSertifikat akan di-generate dalam format PDF dengan:\n- Nama Peserta\n- Nilai Akhir\n- Tanggal Lulus\n- Tanda Tangan Digital Manager QA');
                }
            });
        });
    </script>
@endpush
