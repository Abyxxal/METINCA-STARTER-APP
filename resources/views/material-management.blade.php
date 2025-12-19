{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Material Management')

{{-- Untuk menggunakan css --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
    <style>
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        .media-item {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        .media-item:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .media-thumbnail {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #f8f9fa;
        }
        .media-info {
            padding: 0.75rem;
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
                    {{-- Nama: Material Management --}}
                    {{-- Fungsi: Halaman utama untuk mengelola semua materi pelatihan, dokumen SOP/WI, dan media pembelajaran --}}
                    <h3>Material Management</h3>
                    <p class="text-subtitle text-muted">Kelola katalog pelatihan, dokumen SOP/WI, dan media library</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Material Management</li>
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
                    {{-- SECTION: Tab Navigation untuk Material Management --}}
                    {{-- Fungsi: Navigasi untuk memilih antara 3 jenis materi: Katalog Pelatihan, Pustaka SOP/WI, dan Media Library --}}
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" id="materialManagementTab" role="tablist">
                        {{-- TAB 1: Katalog Pelatihan --}}
                        {{-- Isi: Daftar katalog pelatihan dengan cover image, kategori (Safety/Quality/Technical), target jabatan, deskripsi, status (Active/Inactive) --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="katalog-tab" data-bs-toggle="tab"
                                data-bs-target="#katalog" type="button" role="tab" aria-controls="katalog"
                                aria-selected="true">
                                <i class="bi bi-book-fill me-2"></i>Katalog Pelatihan
                            </button>
                        </li>
                        {{-- TAB 2: Pustaka SOP/WI --}}
                        {{-- Isi: Dokumen SOP dan Work Instruction dengan tracking versi (Rev 1, Rev 2, dll), status (Active/Obsolete), tanggal berlaku --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen"
                                type="button" role="tab" aria-controls="dokumen" aria-selected="false">
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Pustaka SOP/WI
                            </button>
                        </li>
                        {{-- TAB 3: Media Library --}}
                        {{-- Isi: Koleksi media pembelajaran (video MP4, gambar JPG/PNG, YouTube links) dengan preview grid dan info file size --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media"
                                type="button" role="tab" aria-controls="media" aria-selected="false">
                                <i class="bi bi-collection-play-fill me-2"></i>Media Library
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="materialManagementTabContent">
                        {{-- TAB CONTENT 1: Katalog Pelatihan Tab --}}
                        {{-- Fungsi: Menampilkan daftar semua training catalogs dengan fitur CRUD (Create, Read, Update, Delete) --}}
                        <!-- Katalog Pelatihan Tab -->
                        <div class="tab-pane fade show active" id="katalog" role="tabpanel"
                            aria-labelledby="katalog-tab">
                            <div class="mt-4">
                                {{-- Header dengan tombol Tambah Katalog --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Katalog Pelatihan</h5>
                                    <button type="button" class="btn btn-primary" id="btnTambahKatalog">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Katalog
                                    </button>
                                </div>

                                {{-- Tabel Daftar Katalog Pelatihan --}}
                                {{-- Kolom: Cover (cover image), Judul Training (ID pelatihan), Kategori (Safety/Quality/Technical), Target Jabatan (Multi-select roles), Deskripsi, Status (Active/Inactive), Aksi (Detail/Edit/Delete) --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableKatalog">
                                        <thead>
                                            <tr>
                                                <th>Cover</th>
                                                <th>Judul Training</th>
                                                <th>Kategori</th>
                                                <th>Target Jabatan</th>
                                                <th>Deskripsi</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Cover">
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>5S Implementation Training</strong><br>
                                                    <small class="text-muted">ID: TRN-001</small>
                                                </td>
                                                <td><span class="badge bg-light-info">Safety</span></td>
                                                <td>
                                                    <span class="badge bg-light-secondary me-1">Operator</span>
                                                    <span class="badge bg-light-secondary">Leader</span>
                                                </td>
                                                <td>
                                                    <small>Training dasar tentang 5S (Seiri, Seiton, Seiso, Seiketsu, Shitsuke)</small>
                                                </td>
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
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Cover">
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>Quality Control Basic</strong><br>
                                                    <small class="text-muted">ID: TRN-002</small>
                                                </td>
                                                <td><span class="badge bg-light-warning">Quality</span></td>
                                                <td>
                                                    <span class="badge bg-light-secondary">QC Inspector</span>
                                                </td>
                                                <td>
                                                    <small>Pengenalan dasar quality control dan inspection method</small>
                                                </td>
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
                                                <td>
                                                    <div class="avatar avatar-lg">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Cover">
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>Machine Maintenance Training</strong><br>
                                                    <small class="text-muted">ID: TRN-003</small>
                                                </td>
                                                <td><span class="badge bg-light-danger">Technical</span></td>
                                                <td>
                                                    <span class="badge bg-light-secondary me-1">Technician</span>
                                                    <span class="badge bg-light-secondary">Maintenance</span>
                                                </td>
                                                <td>
                                                    <small>Perawatan dan troubleshooting mesin produksi</small>
                                                </td>
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

                        <!-- Pustaka SOP/WI Tab -->
                        {{-- TAB CONTENT 2: Pustaka SOP/WI Tab --}}
                        {{-- Fungsi: Manajemen dokumen SOP dan Work Instruction dengan version control untuk compliance audit --}}
                        <div class="tab-pane fade" id="dokumen" role="tabpanel" aria-labelledby="dokumen-tab">
                            <div class="mt-4">
                                {{-- Header dengan tombol Upload Dokumen --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Pustaka SOP/WI</h5>
                                    <button type="button" class="btn btn-primary" id="btnTambahDokumen">
                                        <i class="bi bi-plus-circle me-1"></i>Upload Dokumen
                                    </button>
                                </div>

                                {{-- Tabel Daftar Dokumen SOP/WI dengan Version Control --}}
                                {{-- Kolom: Nomor Dokumen (WI-QC-001, SOP-PRD-001), Judul Dokumen, Revisi (Rev.1, Rev.2, dll dengan keterangan "Updated from Rev.X"), Tanggal Berlaku, Status (Active/Obsolete), File (PDF download link), Aksi (Upload Revisi/History/Delete) --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableDokumen">
                                        <thead>
                                            <tr>
                                                <th>Nomor Dokumen</th>
                                                <th>Judul Dokumen</th>
                                                <th>Revisi</th>
                                                <th>Tanggal Berlaku</th>
                                                <th>Status</th>
                                                <th>File</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>WI-QC-001</strong></td>
                                                <td>Work Instruction - Visual Inspection</td>
                                                <td>
                                                    <span class="badge bg-light-primary">Rev. 3</span>
                                                    <small class="text-muted d-block">Updated from Rev. 2</small>
                                                </td>
                                                <td>15 Jan 2025</td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-file-earmark-pdf"></i> Download
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1" title="Upload Revisi">
                                                        <i class="bi bi-arrow-up-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info me-1" title="History">
                                                        <i class="bi bi-clock-history"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>WI-QC-002</strong></td>
                                                <td>Work Instruction - Dimensional Check</td>
                                                <td>
                                                    <span class="badge bg-light-primary">Rev. 1</span>
                                                </td>
                                                <td>10 Feb 2025</td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-file-earmark-pdf"></i> Download
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1" title="Upload Revisi">
                                                        <i class="bi bi-arrow-up-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info me-1" title="History">
                                                        <i class="bi bi-clock-history"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td><strong>WI-QC-001</strong></td>
                                                <td>Work Instruction - Visual Inspection</td>
                                                <td>
                                                    <span class="badge bg-secondary">Rev. 2</span>
                                                    <small class="text-muted d-block">Superseded by Rev. 3</small>
                                                </td>
                                                <td>10 Dec 2024</td>
                                                <td><span class="badge bg-secondary">Obsolete</span></td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-file-earmark-pdf"></i> Archive
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" title="View Only">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>SOP-PRD-001</strong></td>
                                                <td>Standard Operating Procedure - Production Line</td>
                                                <td>
                                                    <span class="badge bg-light-primary">Rev. 5</span>
                                                </td>
                                                <td>01 Mar 2025</td>
                                                <td><span class="badge bg-light-success">Active</span></td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-file-earmark-pdf"></i> Download
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1" title="Upload Revisi">
                                                        <i class="bi bi-arrow-up-circle"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-info me-1" title="History">
                                                        <i class="bi bi-clock-history"></i>
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

                        <!-- Media Library Tab -->
                        {{-- TAB CONTENT 3: Media Library Tab --}}
                        {{-- Fungsi: Penyimpanan media pembelajaran dalam bentuk video, gambar, dan YouTube link untuk supporting materi training --}}
                        <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                            <div class="mt-4">
                                {{-- Header dengan dual buttons: Upload Media (untuk file lokal) dan Add YouTube Link --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Media Library</h5>
                                    {{-- Support file types: MP4 video, JPG/PNG images dengan file size tracking --}}
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary" id="btnUploadMedia">
                                            <i class="bi bi-cloud-upload me-1"></i>Upload Media
                                        </button>
                                        <button type="button" class="btn btn-success" id="btnTambahYoutube">
                                            <i class="bi bi-youtube me-1"></i>Add YouTube Link
                                        </button>
                                    </div>
                                </div>

                                {{-- Grid layout untuk menampilkan media items --}}
                                {{-- Setiap item menampilkan: thumbnail preview, nama file, tipe file (MP4/JPG/PNG/YouTube), ukuran file, tanggal upload, tombol watch/hapus --}}
                                <div class="media-grid">
                                    <!-- Video Item -->
                                    <div class="media-item">
                                        <video class="media-thumbnail" controls>
                                            <source src="#" type="video/mp4">
                                        </video>
                                        <div class="media-info">
                                            <strong class="d-block mb-1">Defect Type - Crack</strong>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-file-earmark-play"></i> MP4 | 15.2 MB
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3"></i> 15 Jan 2025
                                            </small>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Image Item -->
                                    <div class="media-item">
                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" class="media-thumbnail" alt="Media">
                                        <div class="media-info">
                                            <strong class="d-block mb-1">Welding Defect Sample</strong>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-file-earmark-image"></i> JPG | 2.5 MB
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3"></i> 10 Jan 2025
                                            </small>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- YouTube Item -->
                                    <div class="media-item">
                                        <div class="media-thumbnail d-flex align-items-center justify-content-center bg-danger text-white">
                                            <i class="bi bi-youtube" style="font-size: 3rem;"></i>
                                        </div>
                                        <div class="media-info">
                                            <strong class="d-block mb-1">Safety Training Video</strong>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-link-45deg"></i> YouTube
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3"></i> 05 Jan 2025
                                            </small>
                                            <div class="mt-2">
                                                <a href="https://youtube.com" target="_blank" class="btn btn-sm btn-outline-danger w-100 mb-1">
                                                    <i class="bi bi-play-circle"></i> Watch
                                                </a>
                                                <button class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Image Item 2 -->
                                    <div class="media-item">
                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" class="media-thumbnail" alt="Media">
                                        <div class="media-info">
                                            <strong class="d-block mb-1">QC Checkpoint Photo</strong>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-file-earmark-image"></i> PNG | 3.8 MB
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3"></i> 20 Dec 2024
                                            </small>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Video Item 2 -->
                                    <div class="media-item">
                                        <video class="media-thumbnail" controls>
                                            <source src="#" type="video/mp4">
                                        </video>
                                        <div class="media-info">
                                            <strong class="d-block mb-1">Assembly Process Tutorial</strong>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-file-earmark-play"></i> MP4 | 28.5 MB
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3"></i> 18 Dec 2024
                                            </small>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Image Item 3 -->
                                    <div class="media-item">
                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" class="media-thumbnail" alt="Media">
                                        <div class="media-info">
                                            <strong class="d-block mb-1">Machine Setup Guide</strong>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-file-earmark-image"></i> JPG | 1.9 MB
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-calendar3"></i> 15 Dec 2024
                                            </small>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-danger w-100">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
            $('#tableKatalog').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[1, 'asc']]
            });

            $('#tableDokumen').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[3, 'desc']] // Sort by date
            });

            // Button handlers
            $('#btnTambahKatalog').on('click', function() {
                alert('Fitur Tambah Katalog Pelatihan akan segera tersedia');
            });

            $('#btnTambahDokumen').on('click', function() {
                alert('Fitur Upload Dokumen SOP/WI akan segera tersedia');
            });

            $('#btnUploadMedia').on('click', function() {
                alert('Fitur Upload Media (Image/Video) akan segera tersedia');
            });

            $('#btnTambahYoutube').on('click', function() {
                alert('Fitur Add YouTube Link akan segera tersedia');
            });
        });
    </script>
@endpush
