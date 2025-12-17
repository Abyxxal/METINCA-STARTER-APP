{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Master Data')

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
                    <h3>Master Data</h3>
                    <p class="text-subtitle text-muted">Kelola data karyawan, departemen, dan jabatan</p>
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
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-justified" id="masterDataTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="karyawan-tab" data-bs-toggle="tab"
                                data-bs-target="#karyawan" type="button" role="tab" aria-controls="karyawan"
                                aria-selected="true">
                                <i class="bi bi-people-fill me-2"></i>Data Karyawan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="departemen-tab" data-bs-toggle="tab" data-bs-target="#departemen"
                                type="button" role="tab" aria-controls="departemen" aria-selected="false">
                                <i class="bi bi-building me-2"></i>Departemen & Line
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="jabatan-tab" data-bs-toggle="tab" data-bs-target="#jabatan"
                                type="button" role="tab" aria-controls="jabatan" aria-selected="false">
                                <i class="bi bi-award-fill me-2"></i>Jabatan
                            </button>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" id="masterDataTabContent">
                        <!-- Data Karyawan Tab -->
                        <div class="tab-pane fade show active" id="karyawan" role="tabpanel"
                            aria-labelledby="karyawan-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Data Karyawan</h5>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary" id="btnTambahKaryawan">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Karyawan
                                        </button>
                                        <button type="button" class="btn btn-success" id="btnImportExcel">
                                            <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableKaryawan">
                                        <thead>
                                            <tr>
                                                <th>Foto</th>
                                                <th>NIK</th>
                                                <th>Nama Lengkap</th>
                                                <th>Departemen</th>
                                                <th>Jabatan</th>
                                                <th>Shift</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP001</td>
                                                <td>Ahmad Fauzi</td>
                                                <td>Quality Control</td>
                                                <td>Leader</td>
                                                <td><span class="badge bg-light-info">Pagi</span></td>
                                                <td><span class="badge bg-light-success">Aktif</span></td>
                                                <td>
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
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP002</td>
                                                <td>Siti Nurhaliza</td>
                                                <td>Produksi</td>
                                                <td>Operator</td>
                                                <td><span class="badge bg-light-warning">Siang</span></td>
                                                <td><span class="badge bg-light-success">Aktif</span></td>
                                                <td>
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
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP003</td>
                                                <td>Budi Santoso</td>
                                                <td>Maintenance</td>
                                                <td>Supervisor</td>
                                                <td><span class="badge bg-light-dark">Malam</span></td>
                                                <td><span class="badge bg-light-success">Aktif</span></td>
                                                <td>
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
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP004</td>
                                                <td>Dewi Lestari</td>
                                                <td>Quality Control</td>
                                                <td>Operator</td>
                                                <td><span class="badge bg-light-info">Pagi</span></td>
                                                <td><span class="badge bg-light-danger">Resign</span></td>
                                                <td>
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

                        <!-- Departemen & Line Tab -->
                        <div class="tab-pane fade" id="departemen" role="tabpanel" aria-labelledby="departemen-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Departemen & Line</h5>
                                    <button type="button" class="btn btn-primary" id="btnTambahDepartemen">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Departemen
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableDepartemen">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Departemen</th>
                                                <th>Nama Line</th>
                                                <th>Nama Gedung</th>
                                                <th>Jumlah Karyawan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Quality Control</td>
                                                <td>Line A</td>
                                                <td>Gedung 1</td>
                                                <td>25</td>
                                                <td>
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
                                                <td>Produksi</td>
                                                <td>Line B</td>
                                                <td>Gedung 2</td>
                                                <td>50</td>
                                                <td>
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
                                                <td>Maintenance</td>
                                                <td>Line C</td>
                                                <td>Gedung 1</td>
                                                <td>15</td>
                                                <td>
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
                                                <td>Warehouse</td>
                                                <td>Line D</td>
                                                <td>Gedung 3</td>
                                                <td>20</td>
                                                <td>
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

                        <!-- Jabatan Tab -->
                        <div class="tab-pane fade" id="jabatan" role="tabpanel" aria-labelledby="jabatan-tab">
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Jabatan</h5>
                                    <button type="button" class="btn btn-primary" id="btnTambahJabatan">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableJabatan">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Jabatan</th>
                                                <th>Level</th>
                                                <th>Keterangan</th>
                                                <th>Jumlah Karyawan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Operator</td>
                                                <td><span class="badge bg-light-info">Level 1</span></td>
                                                <td>Pelaksana operasional harian</td>
                                                <td>60</td>
                                                <td>
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
                                                <td>Leader</td>
                                                <td><span class="badge bg-light-warning">Level 2</span></td>
                                                <td>Memimpin tim operator</td>
                                                <td>20</td>
                                                <td>
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
                                                <td>Supervisor</td>
                                                <td><span class="badge bg-light-success">Level 3</span></td>
                                                <td>Mengawasi beberapa line produksi</td>
                                                <td>10</td>
                                                <td>
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
                                                <td>Manager</td>
                                                <td><span class="badge bg-light-danger">Level 4</span></td>
                                                <td>Mengelola departemen</td>
                                                <td>5</td>
                                                <td>
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

            // Initialize DataTables
            $('#tableKaryawan').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[1, 'asc']]
            });

            $('#tableDepartemen').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10
            });

            $('#tableJabatan').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10
            });

            // Button handlers (placeholder for future implementation)
            $('#btnTambahKaryawan').on('click', function() {
                alert('Fitur Tambah Karyawan akan segera tersedia');
            });

            $('#btnImportExcel').on('click', function() {
                alert('Fitur Import Excel akan segera tersedia');
            });

            $('#btnTambahDepartemen').on('click', function() {
                alert('Fitur Tambah Departemen akan segera tersedia');
            });

            $('#btnTambahJabatan').on('click', function() {
                alert('Fitur Tambah Jabatan akan segera tersedia');
            });
        });
    </script>
@endpush