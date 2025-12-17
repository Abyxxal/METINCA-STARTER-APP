{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Evaluation & Exam')

{{-- Untuk menggunakan css --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
@endpush

{{-- Isi content --}}
@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Evaluation & Exam</h3>
                    <p class="text-subtitle text-muted">Kelola bank soal, setup ujian, dan hasil ujian</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Evaluation & Exam</li>
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
                    <ul class="nav nav-tabs nav-justified" id="evaluationExamTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="banksoal-tab" data-bs-toggle="tab"
                                data-bs-target="#banksoal" type="button" role="tab" aria-controls="banksoal"
                                aria-selected="true">
                                <i class="bi bi-question-circle-fill me-2"></i>Bank Soal
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="setupujian-tab" data-bs-toggle="tab" data-bs-target="#setupujian"
                                type="button" role="tab" aria-controls="setupujian" aria-selected="false">
                                <i class="bi bi-file-earmark-text-fill me-2"></i>Setup Ujian
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="hasilujian-tab" data-bs-toggle="tab" data-bs-target="#hasilujian"
                                type="button" role="tab" aria-controls="hasilujian" aria-selected="false">
                                <i class="bi bi-clipboard-check-fill me-2"></i>Hasil Ujian
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="evaluationExamTabContent">
                        <!-- Bank Soal Tab -->
                        <div class="tab-pane fade show active" id="banksoal" role="tabpanel"
                            aria-labelledby="banksoal-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Bank Soal</h5>
                                    <div>
                                        <select class="form-select d-inline-block w-auto me-2" id="filterMateri">
                                            <option value="">Semua Materi</option>
                                            <option value="GMP">GMP (Good Manufacturing Practice)</option>
                                            <option value="5R">5R (Ringkas, Rapi, Resik, Rawat, Rajin)</option>
                                            <option value="Safety">Safety & K3</option>
                                            <option value="Quality">Quality Control</option>
                                            <option value="Technical">Technical Skill</option>
                                        </select>
                                        <button type="button" class="btn btn-primary" id="btnTambahSoal">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Soal
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableBankSoal">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Materi</th>
                                                <th>Pertanyaan</th>
                                                <th>Jawaban</th>
                                                <th>Bobot</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td><span class="badge bg-light-info">GMP</span></td>
                                                <td>
                                                    <strong>Apa kepanjangan dari GMP?</strong><br>
                                                    <small class="text-muted">
                                                        A. Good Making Product<br>
                                                        B. Good Manufacturing Practice<br>
                                                        C. Great Making Process<br>
                                                        D. Great Manufacturing Product
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">B</span>
                                                </td>
                                                <td>10 poin</td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td><span class="badge bg-light-warning">5R</span></td>
                                                <td>
                                                    <strong>Manakah yang BUKAN termasuk dalam 5R?</strong><br>
                                                    <small class="text-muted">
                                                        A. Ringkas<br>
                                                        B. Rapi<br>
                                                        C. Ramah<br>
                                                        D. Rajin
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">C</span>
                                                </td>
                                                <td>10 poin</td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td><span class="badge bg-light-danger">Safety</span></td>
                                                <td>
                                                    <strong>APD yang wajib digunakan di area produksi adalah?</strong><br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-image text-info"></i> <em>Gambar terlampir</em><br>
                                                        A. Helm, Sarung Tangan, Safety Shoes<br>
                                                        B. Masker, Kacamata, Earplugs<br>
                                                        C. Semua jawaban benar<br>
                                                        D. Hanya A yang benar
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">C</span>
                                                </td>
                                                <td>15 poin</td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td><span class="badge bg-light-primary">Quality</span></td>
                                                <td>
                                                    <strong>Apa yang dimaksud dengan Quality Control?</strong><br>
                                                    <small class="text-muted">
                                                        A. Proses mengecek produk akhir<br>
                                                        B. Sistem pencegahan cacat produk<br>
                                                        C. Metode perbaikan kualitas<br>
                                                        D. Semua proses pengendalian mutu produk
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">D</span>
                                                </td>
                                                <td>10 poin</td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Setup Ujian Tab -->
                        <div class="tab-pane fade" id="setupujian" role="tabpanel" aria-labelledby="setupujian-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Setup Ujian</h5>
                                    <button type="button" class="btn btn-primary" id="btnTambahUjian">
                                        <i class="bi bi-plus-circle me-1"></i>Buat Ujian Baru
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableSetupUjian">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Judul Ujian</th>
                                                <th>Materi</th>
                                                <th>Durasi</th>
                                                <th>Passing Grade</th>
                                                <th>Batas Percobaan</th>
                                                <th>Mode Acak</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>EX-001</strong></td>
                                                <td>
                                                    <strong>Ujian GMP Dasar</strong><br>
                                                    <small class="text-muted">20 Soal</small>
                                                </td>
                                                <td><span class="badge bg-light-info">GMP</span></td>
                                                <td>
                                                    <i class="bi bi-clock"></i> 30 menit
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-warning">80%</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-secondary">Max 3x</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">
                                                        <i class="bi bi-shuffle"></i> Ya
                                                    </span>
                                                </td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>EX-002</strong></td>
                                                <td>
                                                    <strong>Ujian 5R Lanjutan</strong><br>
                                                    <small class="text-muted">15 Soal</small>
                                                </td>
                                                <td><span class="badge bg-light-warning">5R</span></td>
                                                <td>
                                                    <i class="bi bi-clock"></i> 20 menit
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-warning">75%</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-secondary">Max 2x</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-danger">
                                                        <i class="bi bi-x-circle"></i> Tidak
                                                    </span>
                                                </td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>EX-003</strong></td>
                                                <td>
                                                    <strong>Ujian Safety & K3</strong><br>
                                                    <small class="text-muted">25 Soal</small>
                                                </td>
                                                <td><span class="badge bg-light-danger">Safety</span></td>
                                                <td>
                                                    <i class="bi bi-clock"></i> 45 menit
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-warning">85%</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-secondary">Max 3x</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">
                                                        <i class="bi bi-shuffle"></i> Ya
                                                    </span>
                                                </td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Preview">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td><strong>EX-004</strong></td>
                                                <td>
                                                    <strong>Ujian Quality Control</strong><br>
                                                    <small class="text-muted">30 Soal</small>
                                                </td>
                                                <td><span class="badge bg-secondary">Quality</span></td>
                                                <td>
                                                    <i class="bi bi-clock"></i> 60 menit
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">80%</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">Max 3x</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-shuffle"></i> Ya
                                                    </span>
                                                </td>
                                                <td><span class="badge bg-secondary">Draft</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-success me-1" title="Publish">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning me-1" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Hasil Ujian Tab -->
                        <div class="tab-pane fade" id="hasilujian" role="tabpanel" aria-labelledby="hasilujian-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Hasil Ujian</h5>
                                    <div>
                                        <select class="form-select d-inline-block w-auto me-2" id="filterUjian">
                                            <option value="">Semua Ujian</option>
                                            <option value="EX-001">Ujian GMP Dasar</option>
                                            <option value="EX-002">Ujian 5R Lanjutan</option>
                                            <option value="EX-003">Ujian Safety & K3</option>
                                        </select>
                                        <button type="button" class="btn btn-success" id="btnExportHasil">
                                            <i class="bi bi-file-excel me-1"></i>Export Excel
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableHasilUjian">
                                        <thead>
                                            <tr>
                                                <th>NIK</th>
                                                <th>Nama Peserta</th>
                                                <th>Ujian</th>
                                                <th>Tanggal</th>
                                                <th>Skor</th>
                                                <th>Percobaan Ke-</th>
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
                                                        <strong>Ahmad Fauzi</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-info">EX-001</span><br>
                                                    <small class="text-muted">Ujian GMP Dasar</small>
                                                </td>
                                                <td>
                                                    15 Jan 2025<br>
                                                    <small class="text-muted">14:30 WIB</small>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0 text-success">85</h5>
                                                    <small class="text-muted">/ 100</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-primary">1</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">
                                                        <i class="bi bi-check-circle"></i> LULUS
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary" title="Cetak Sertifikat">
                                                        <i class="bi bi-printer"></i>
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
                                                <td>
                                                    <span class="badge bg-light-info">EX-001</span><br>
                                                    <small class="text-muted">Ujian GMP Dasar</small>
                                                </td>
                                                <td>
                                                    15 Jan 2025<br>
                                                    <small class="text-muted">15:00 WIB</small>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0 text-danger">65</h5>
                                                    <small class="text-muted">/ 100</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-warning">2</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-danger">
                                                        <i class="bi bi-x-circle"></i> GAGAL
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" title="Reset (Beri Kesempatan Ulang)">
                                                        <i class="bi bi-arrow-clockwise"></i>
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
                                                <td>
                                                    <span class="badge bg-light-warning">EX-002</span><br>
                                                    <small class="text-muted">Ujian 5R Lanjutan</small>
                                                </td>
                                                <td>
                                                    14 Jan 2025<br>
                                                    <small class="text-muted">10:15 WIB</small>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0 text-success">90</h5>
                                                    <small class="text-muted">/ 100</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-primary">1</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">
                                                        <i class="bi bi-check-circle"></i> LULUS
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary" title="Cetak Sertifikat">
                                                        <i class="bi bi-printer"></i>
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
                                                <td>
                                                    <span class="badge bg-light-danger">EX-003</span><br>
                                                    <small class="text-muted">Ujian Safety & K3</small>
                                                </td>
                                                <td>
                                                    13 Jan 2025<br>
                                                    <small class="text-muted">13:45 WIB</small>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0 text-success">88</h5>
                                                    <small class="text-muted">/ 100</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-primary">1</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-success">
                                                        <i class="bi bi-check-circle"></i> LULUS
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary" title="Cetak Sertifikat">
                                                        <i class="bi bi-printer"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2024005</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                                        </div>
                                                        <strong>Rudi Hermawan</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-info">EX-001</span><br>
                                                    <small class="text-muted">Ujian GMP Dasar</small>
                                                </td>
                                                <td>
                                                    12 Jan 2025<br>
                                                    <small class="text-muted">16:20 WIB</small>
                                                </td>
                                                <td>
                                                    <h5 class="mb-0 text-warning">75</h5>
                                                    <small class="text-muted">/ 100</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-danger">3</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-danger">
                                                        <i class="bi bi-x-circle"></i> GAGAL
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info me-1" title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" title="Reset (Beri Kesempatan Ulang)">
                                                        <i class="bi bi-arrow-clockwise"></i>
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
            $('#tableBankSoal').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[0, 'asc']]
            });

            $('#tableSetupUjian').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[0, 'asc']]
            });

            $('#tableHasilUjian').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[3, 'desc']] // Sort by date
            });

            // Filter handlers
            $('#filterMateri').on('change', function() {
                var materi = $(this).val();
                if (materi) {
                    $('#tableBankSoal').DataTable().column(1).search(materi).draw();
                } else {
                    $('#tableBankSoal').DataTable().column(1).search('').draw();
                }
            });

            $('#filterUjian').on('change', function() {
                var ujian = $(this).val();
                if (ujian) {
                    $('#tableHasilUjian').DataTable().column(2).search(ujian).draw();
                } else {
                    $('#tableHasilUjian').DataTable().column(2).search('').draw();
                }
            });

            // Button handlers
            $('#btnTambahSoal').on('click', function() {
                alert('Fitur Tambah Soal akan segera tersedia');
            });

            $('#btnTambahUjian').on('click', function() {
                alert('Fitur Buat Ujian Baru akan segera tersedia');
            });

            $('#btnExportHasil').on('click', function() {
                alert('Fitur Export Excel akan segera tersedia');
            });
        });
    </script>
@endpush
