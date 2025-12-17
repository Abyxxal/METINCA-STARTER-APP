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
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" id="reportAuditTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="matrikskompetensi-tab" data-bs-toggle="tab"
                                data-bs-target="#matrikskompetensi" type="button" role="tab" aria-controls="matrikskompetensi"
                                aria-selected="true">
                                <i class="bi bi-grid-3x3-gap-fill me-2"></i>Matriks Kompetensi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="riwayatpelatihan-tab" data-bs-toggle="tab" data-bs-target="#riwayatpelatihan"
                                type="button" role="tab" aria-controls="riwayatpelatihan" aria-selected="false">
                                <i class="bi bi-clock-history me-2"></i>Riwayat Pelatihan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cetaksertifikat-tab" data-bs-toggle="tab" data-bs-target="#cetaksertifikat"
                                type="button" role="tab" aria-controls="cetaksertifikat" aria-selected="false">
                                <i class="bi bi-award-fill me-2"></i>Cetak Sertifikat
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="reportAuditTabContent">
                        <!-- Matriks Kompetensi Tab -->
                        <div class="tab-pane fade show active" id="matrikskompetensi" role="tabpanel"
                            aria-labelledby="matrikskompetensi-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Matriks Kompetensi Karyawan</h5>
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

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Matriks ini menunjukkan status kompetensi karyawan untuk setiap skill/training. 
                                    File Excel hasil export dapat digunakan untuk audit ISO 9001.
                                </div>

                                <div class="matrix-legend">
                                    <div class="legend-item">
                                        <div class="legend-box cell-competent"></div>
                                        <span>Kompeten / Lulus</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-box cell-not-passed"></div>
                                        <span>Belum Lulus</span>
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-box cell-not-trained"></div>
                                        <span>Belum Training</span>
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
                                                    <i class="bi bi-check-circle-fill"></i><br>85%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>90%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>88%
                                                </td>
                                                <td class="cell-not-passed">
                                                    <i class="bi bi-x-circle-fill"></i><br>65%
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>82%
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
                                                    <i class="bi bi-check-circle-fill"></i><br>92%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>95%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>90%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>95%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>88%
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
                                                    <i class="bi bi-check-circle-fill"></i><br>85%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>90%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>93%
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>95%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>92%
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
                                                    <i class="bi bi-check-circle-fill"></i><br>98%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>95%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>96%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>88%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>85%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>90%
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>92%
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    2024005<br>
                                                    <small class="text-muted">Rudi Hermawan</small><br>
                                                    <small class="badge bg-light-primary">Operator</small>
                                                </th>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>80%
                                                </td>
                                                <td class="cell-not-passed">
                                                    <i class="bi bi-x-circle-fill"></i><br>72%
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>85%
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-not-trained">
                                                    <i class="bi bi-dash-circle"></i><br>-
                                                </td>
                                                <td class="cell-competent">
                                                    <i class="bi bi-check-circle-fill"></i><br>88%
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
                        <div class="tab-pane fade" id="riwayatpelatihan" role="tabpanel" aria-labelledby="riwayatpelatihan-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Riwayat Pelatihan Karyawan</h5>
                                    <div>
                                        <input type="text" class="form-control d-inline-block w-auto me-2" 
                                            placeholder="Cari NIK atau Nama..." id="searchKaryawan">
                                        <button type="button" class="btn btn-success" id="btnExportRiwayat">
                                            <i class="bi bi-file-excel me-1"></i>Export Excel
                                        </button>
                                    </div>
                                </div>

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
                        <div class="tab-pane fade" id="cetaksertifikat" role="tabpanel" aria-labelledby="cetaksertifikat-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Cetak Sertifikat Karyawan</h5>
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

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Sertifikat akan di-generate dalam format PDF dengan template standar perusahaan. 
                                    Termasuk: Nama Peserta, Nilai Akhir, Tanggal Lulus, dan Tanda Tangan Digital Manager QA.
                                </div>

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

            // Filter handlers
            $('#filterDepartemen').on('change', function() {
                alert('Filter Departemen: ' + $(this).val());
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
