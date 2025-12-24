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
                            <div class="mt-4">
                                {{-- Header dengan tombol Tambah Karyawan dan Import Excel --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Data Karyawan</h5>
                                    {{-- Support for individual creation dan bulk import via Excel file --}}
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary" id="btnTambahKaryawan">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Karyawan
                                        </button>
                                        <button type="button" class="btn btn-success" id="btnImportExcel">
                                            <i class="bi bi-file-earmark-excel me-1"></i>Import Excel
                                        </button>
                                    </div>
                                </div>

                                {{-- Filter Departemen --}}
                                <div class="mb-3">
                                    <label for="filterDepartemenKaryawan" class="form-label">Filter Departemen:</label>
                                    <select id="filterDepartemenKaryawan" class="form-select" style="max-width: 400px;">
                                        <option value="">Semua Departemen</option>
                                        <option value="Quality">Quality</option>
                                        <option value="Maintenance">Maintenance</option>
                                        <option value="Production Planning & Control">Production Planning & Control</option>
                                        <option value="Produksi & Development Engineering">Produksi & Development Engineering</option>
                                    </select>
                                </div>

                                {{-- Tabel Data Karyawan --}}
                                {{-- Kolom: No, Foto, NIK, Nama Lengkap, Departemen, Jabatan, Status, Aksi --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableKaryawan">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Foto</th>
                                                <th>NIK</th>
                                                <th>Nama Lengkap</th>
                                                <th>Departemen</th>
                                                <th>Jabatan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Quality Department - 10 employees --}}
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP001</td>
                                                <td>Ahmad Fauzi</td>
                                                <td>Quality</td>
                                                <td>Manager Quality</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit" data-id="1" data-nik="EMP001" data-nama="Ahmad Fauzi" data-departemen="Quality" data-jabatan="Manager Quality" data-status="Aktif" data-foto="{{ asset('assets/compiled/jpg/1.jpg') }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP002</td>
                                                <td>Budi Santoso</td>
                                                <td>Quality</td>
                                                <td>Assistant Manager Quality</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP003</td>
                                                <td>Citra Dewi</td>
                                                <td>Quality</td>
                                                <td>Supervisor Quality Control</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP004</td>
                                                <td>Dedi Prasetyo</td>
                                                <td>Quality</td>
                                                <td>QA Engineer</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP005</td>
                                                <td>Eka Sari</td>
                                                <td>Quality</td>
                                                <td>Foreman QC</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>6</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP006</td>
                                                <td>Fajar Nugroho</td>
                                                <td>Quality</td>
                                                <td>Inspektor QC</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>7</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP007</td>
                                                <td>Gita Permata</td>
                                                <td>Quality</td>
                                                <td>Staff Admin QC</td>
                                                <td><span class="badge bg-warning text-dark">Non-Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>8</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP008</td>
                                                <td>Hadi Wijaya</td>
                                                <td>Quality</td>
                                                <td>Staff Admin QA</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>9</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP009</td>
                                                <td>Indah Lestari</td>
                                                <td>Quality</td>
                                                <td>Staff HSE</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>10</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP010</td>
                                                <td>Joko Susilo</td>
                                                <td>Quality</td>
                                                <td>Manager Quality</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            {{-- Maintenance Department - 9 employees --}}
                                            <tr>
                                                <td>11</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP011</td>
                                                <td>Kurnia Rahman</td>
                                                <td>Maintenance</td>
                                                <td>Manager Maintenance</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>12</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP012</td>
                                                <td>Lina Marlina</td>
                                                <td>Maintenance</td>
                                                <td>Supervisor Maintenance</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>13</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP013</td>
                                                <td>Muhamad Yusuf</td>
                                                <td>Maintenance</td>
                                                <td>Foreman Maintenance</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>14</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP014</td>
                                                <td>Nur Hidayat</td>
                                                <td>Maintenance</td>
                                                <td>Operator Maintenance Listrik</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>15</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP015</td>
                                                <td>Oktavia Sari</td>
                                                <td>Maintenance</td>
                                                <td>Operator Maintenance Umum</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>16</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP016</td>
                                                <td>Priyanto Adi</td>
                                                <td>Maintenance</td>
                                                <td>Operator Maintenance Listrik</td>
                                                <td><span class="badge bg-warning text-dark">Non-Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>17</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP017</td>
                                                <td>Qori Aini</td>
                                                <td>Maintenance</td>
                                                <td>Operator Maintenance Umum</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>18</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP018</td>
                                                <td>Rudi Hartono</td>
                                                <td>Maintenance</td>
                                                <td>Operator Maintenance Umum</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>19</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP019</td>
                                                <td>Siti Nurhaliza</td>
                                                <td>Maintenance</td>
                                                <td>Operator Maintenance Umum</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            {{-- Production Planning & Control Department - 8 employees --}}
                                            <tr>
                                                <td>20</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP020</td>
                                                <td>Taufik Hidayat</td>
                                                <td>Production Planning & Control</td>
                                                <td>Manager Production, Planning & Control</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>21</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP021</td>
                                                <td>Umar Bakri</td>
                                                <td>Production Planning & Control</td>
                                                <td>Staff PPIC</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>22</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP022</td>
                                                <td>Vina Anggraini</td>
                                                <td>Production Planning & Control</td>
                                                <td>Supervisor Gudang</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>23</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP023</td>
                                                <td>Wahyu Saputra</td>
                                                <td>Production Planning & Control</td>
                                                <td>Foreman Gudang</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>24</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP024</td>
                                                <td>Xena Kartika</td>
                                                <td>Production Planning & Control</td>
                                                <td>Operator Gudang</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>25</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP025</td>
                                                <td>Yanto Prabowo</td>
                                                <td>Production Planning & Control</td>
                                                <td>Operator Gudang</td>
                                                <td><span class="badge bg-warning text-dark">Non-Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>26</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP026</td>
                                                <td>Zahra Amelia</td>
                                                <td>Production Planning & Control</td>
                                                <td>Operator Gudang</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>27</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP027</td>
                                                <td>Andi Firmansyah</td>
                                                <td>Production Planning & Control</td>
                                                <td>Supervisor Gudang</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            {{-- Produksi & Development Engineering Department - 19 employees --}}
                                            <tr>
                                                <td>28</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP028</td>
                                                <td>Bambang Sutrisno</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Manager Produksi & Development Engineering</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>29</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP029</td>
                                                <td>Candra Wijaya</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Supervisor Development Engineer</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>30</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP030</td>
                                                <td>Dewi Kusuma</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Staff Development Engineer</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>31</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP031</td>
                                                <td>Eko Prasetyo</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Supervisor Wax Room</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>32</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP032</td>
                                                <td>Fitri Handayani</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Foreman Wax Room</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>33</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP033</td>
                                                <td>Guntur Wicaksono</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Operator Wax Room</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>34</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP034</td>
                                                <td>Hendra Setiawan</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Supervisor Mould Room</td>
                                                <td><span class="badge bg-warning text-dark">Non-Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>35</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP035</td>
                                                <td>Irma Suryani</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Foreman Mould Room</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>36</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP036</td>
                                                <td>Jaka Firmansyah</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Operator Mould Room</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>37</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP037</td>
                                                <td>Kartika Sari</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Supervisor Melting</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>38</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP038</td>
                                                <td>Lukman Hakim</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Foreman Melting</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>39</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP039</td>
                                                <td>Maya Susanti</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Operator Melting</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>40</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP040</td>
                                                <td>Nanda Pratama</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Supervisor Cut Off</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>41</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP041</td>
                                                <td>Oki Setiawan</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Foreman Cut Off</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>42</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/2.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP042</td>
                                                <td>Putri Ayu</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Operator Cut Off</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>43</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/3.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP043</td>
                                                <td>Rizki Ramadhan</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Supervisor Finishing & Straightening</td>
                                                <td><span class="badge bg-warning text-dark">Non-Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>44</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/4.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP044</td>
                                                <td>Sari Wulandari</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Foreman Finishing & Straightening</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>45</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/5.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP045</td>
                                                <td>Tono Sugiarto</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Operator Finishing & Straightening</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>46</td>
                                                <td>
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Foto">
                                                    </div>
                                                </td>
                                                <td>EMP046</td>
                                                <td>Uswatun Hasanah</td>
                                                <td>Produksi & Development Engineering</td>
                                                <td>Supervisor Machining</td>
                                                <td><span class="badge bg-success">Aktif</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-karyawan" title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-karyawan" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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
                            <div class="mt-4">
                                {{-- Header dengan dropdown filter departemen dan tombol Tambah Departemen --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="mb-0">Departemen</h5>
                                        <select class="form-select w-auto" id="filterDepartemenMaster">
                                            <option value="">Semua Departemen</option>
                                            <option value="Quality">Quality</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="PPC">PPC</option>
                                            <option value="Produksi & Dev Engineering">Produksi & Dev Engineering</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="btnTambahDepartemen">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Departemen
                                    </button>
                                </div>

                                {{-- Tabel Departemen --}}
                                {{-- Kolom: No (nomor urut), Nama Departemen (department name), Jumlah Karyawan (employee count), Aksi (Edit/Delete) --}}
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableDepartemen">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Departemen</th>
                                                <th>Jumlah Karyawan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Departemen Quality - 10 karyawan -->
                                            <tr>
                                                <td>1</td>
                                                <td>Quality</td>
                                                <td>10</td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-departemen" title="Edit"
                                                        data-id="1" data-departemen="Quality" data-jumlah="10">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-departemen" title="Hapus"
                                                        data-id="1" data-departemen="Quality">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Departemen Maintenance - 9 karyawan -->
                                            <tr>
                                                <td>2</td>
                                                <td>Maintenance</td>
                                                <td>9</td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-departemen" title="Edit"
                                                        data-id="2" data-departemen="Maintenance" data-jumlah="9">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-departemen" title="Hapus"
                                                        data-id="2" data-departemen="Maintenance">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Departemen PPC - 8 karyawan -->
                                            <tr>
                                                <td>3</td>
                                                <td>PPC</td>
                                                <td>8</td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-departemen" title="Edit"
                                                        data-id="3" data-departemen="PPC" data-jumlah="8">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-departemen" title="Hapus"
                                                        data-id="3" data-departemen="PPC">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Departemen Produksi & Dev Engineering - 19 karyawan -->
                                            <tr>
                                                <td>4</td>
                                                <td>Produksi & Dev Engineering</td>
                                                <td>19</td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1 btn-edit-departemen" title="Edit"
                                                        data-id="4" data-departemen="Produksi & Dev Engineering" data-jumlah="19">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-hapus-departemen" title="Hapus"
                                                        data-id="4" data-departemen="Produksi & Dev Engineering">
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

        {{-- Modal Tambah Departemen --}}
        <div class="modal fade" id="modalTambahDepartemen" tabindex="-1" aria-labelledby="modalTambahDepartemenLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahDepartemenLabel">Tambah Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahDepartemen">
                            <div class="mb-3">
                                <label for="namaDepartemen" class="form-label">Nama Departemen</label>
                                <input type="text" class="form-control" id="namaDepartemen" placeholder="Masukkan nama departemen baru" required>
                                <small class="text-muted">Contoh: Quality, Maintenance, PPC, Produksi & Dev Engineering, dll.</small>
                            </div>
                            <div class="mb-3">
                                <label for="jumlahKaryawan" class="form-label">Jumlah Karyawan</label>
                                <input type="number" class="form-control" id="jumlahKaryawan" placeholder="0" min="0" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnSimpanDepartemen">
                            <i class="bi bi-save me-1"></i>Simpan
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
                            <div class="mb-3">
                                <label for="editJumlahKaryawan" class="form-label">Jumlah Karyawan</label>
                                <input type="number" class="form-control" id="editJumlahKaryawan" placeholder="0" min="0" required>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fotoKaryawan" class="form-label">Foto</label>
                                        <input type="file" class="form-control" id="fotoKaryawan" accept="image/*">
                                        <div id="previewFoto" class="mt-2">
                                            <img id="imageFotoPreview" src="" alt="Preview" style="max-width: 150px; max-height: 150px; display: none; border-radius: 5px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nikKaryawan" class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nikKaryawan" placeholder="Contoh: EMP047" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="namaKaryawan" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namaKaryawan" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="departemenKaryawan" class="form-label">Departemen <span class="text-danger">*</span></label>
                                        <select class="form-select" id="departemenKaryawan" required>
                                            <option value="">-- Pilih Departemen --</option>
                                            <option value="1">Quality</option>
                                            <option value="2">Maintenance</option>
                                            <option value="3">PPC</option>
                                            <option value="4">Produksi & Dev Engineering</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jabatanKaryawan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="jabatanKaryawan" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="statusKaryawan" id="statusAktif" value="active" checked>
                                    <label class="form-check-label" for="statusAktif">
                                        <span class="badge bg-success">Aktif</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="statusKaryawan" id="statusNonAktif" value="inactive">
                                    <label class="form-check-label" for="statusNonAktif">
                                        <span class="badge bg-warning text-dark">Non-Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSimpanKaryawan">
                            <i class="bi bi-check-circle me-1"></i>Simpan
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
                                        <label for="editNikKaryawan" class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNikKaryawan" placeholder="Contoh: EMP047" required>
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
                                        <label for="editDepartemenKaryawan" class="form-label">Departemen <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editDepartemenKaryawan" required>
                                            <option value="">-- Pilih Departemen --</option>
                                            <option value="Quality">Quality</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Production Planning & Control">Production Planning & Control</option>
                                            <option value="Produksi & Development Engineering">Produksi & Development Engineering</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editJabatanKaryawan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editJabatanKaryawan" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusAktif" value="Aktif">
                                    <label class="form-check-label" for="editStatusAktif">
                                        <span class="badge bg-success">Aktif</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusNonAktif" value="Non-Aktif">
                                    <label class="form-check-label" for="editStatusNonAktif">
                                        <span class="badge bg-warning text-dark">Non-Aktif</span>
                                    </label>
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
            var tableKaryawan = $('#tableKaryawan').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                order: [[2, 'asc']],
                drawCallback: function() {
                    var api = this.api();
                    api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });

            // Filter departemen karyawan dropdown
            $('#filterDepartemenKaryawan').on('change', function() {
                var selectedDept = $(this).val();
                tableKaryawan.column(4).search(selectedDept).draw();
            });

            var tableDepartemen = $('#tableDepartemen').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                pageLength: 10,
                drawCallback: function() {
                    var api = this.api();
                    api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
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
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
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

            // Update Jabatan dropdown ketika Departemen berubah
            $('#departemenKaryawan').on('change', function() {
                var departmentId = $(this).val();
                var jabatanSelect = $('#jabatanKaryawan');

                if (!departmentId) {
                    jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                    return;
                }

                // Ambil data jabatan dari hardcoded mapping
                var jabatanList = jabatanByDepartemen[departmentId] || [];
                jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');

                // Load position IDs dari API dan tambahkan ke dropdown
                $.ajax({
                    url: '/api/positions?department_id=' + departmentId,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Create mapping of position name to ID
                            var positionMap = {};
                            response.data.forEach(function(position) {
                                positionMap[position.name] = position.id;
                            });

                            // Populate dropdown dengan data dari hardcoded mapping dan ID dari API
                            jabatanList.forEach(function(jabatanName) {
                                var positionId = positionMap[jabatanName];
                                if (positionId) {
                                    jabatanSelect.append('<option value="' + positionId + '">' + jabatanName + '</option>');
                                }
                            });
                        }
                    },
                    error: function() {
                        // Fallback: jika API gagal, tetap tampilkan nama jabatan (tapi dengan value text, bukan ID)
                        jabatanList.forEach(function(jabatanName) {
                            jabatanSelect.append('<option value="' + jabatanName + '">' + jabatanName + '</option>');
                        });
                    }
                });
            });

            // Button handlers
            $('#btnTambahKaryawan').on('click', function() {
                $('#formTambahKaryawan')[0].reset();
                $('#imageFotoPreview').hide();
                $('#departemenKaryawan').val('');
                $('#jabatanKaryawan').html('<option value="">-- Pilih Jabatan --</option>');
                $('#modalTambahKaryawan').modal('show');
            });

            $('#btnImportExcel').on('click', function() {
                alert('Fitur Import Excel akan segera tersedia');
            });

            $('#btnTambahDepartemen').on('click', function() {
                $('#formTambahDepartemen')[0].reset();
                $('#modalTambahDepartemen').modal('show');
            });

            $('#btnSimpanKaryawan').on('click', function() {
                var nik = $('#nikKaryawan').val();
                var nama = $('#namaKaryawan').val();
                var departemenId = $('#departemenKaryawan').val();
                var jabatanId = $('#jabatanKaryawan').val();
                var status = $('input[name="statusKaryawan"]:checked').val();

                console.log('Form Data:', {
                    nik: nik,
                    name: nama,
                    department_id: departemenId,
                    position_id: jabatanId,
                    status: status
                });

                if (!nik || !nama || !departemenId || !jabatanId) {
                    alert('Semua field harus diisi!');
                    console.log('Validation failed - missing fields');
                    return;
                }

                var submitBtn = $(this);
                submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass me-1"></i>Menyimpan...');

                $.ajax({
                    url: '/api/employees',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        nik: nik,
                        name: nama,
                        department_id: parseInt(departemenId),
                        position_id: parseInt(jabatanId),
                        status: status || 'active'
                    }),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Success Response:', response);
                        if (response.success) {
                            alert('Data Karyawan berhasil disimpan!');
                            $('#modalTambahKaryawan').modal('hide');
                            $('#formTambahKaryawan')[0].reset();
                            tableKaryawan.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        console.log('Error Response:', xhr.responseJSON);
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan:\n';
                        for (let key in errors) {
                            errorMsg += '- ' + key + ': ' + errors[key][0] + '\n';
                        }
                        alert(errorMsg || 'Gagal menyimpan data: ' + xhr.responseJSON.message);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Simpan');
                    }
                });
            });

            // Handle Edit Karyawan button click
            $(document).on('click', '.btn-edit-karyawan', function() {
                var row = $(this).closest('tr');
                var nik = row.find('td').eq(2).text(); // Column 2: NIK
                var nama = row.find('td').eq(3).text(); // Column 3: Nama
                var departemen = row.find('td').eq(4).text(); // Column 4: Departemen
                var jabatan = row.find('td').eq(5).text(); // Column 5: Jabatan
                var statusText = row.find('td').eq(6).text().trim(); // Column 6: Status
                var fotoUrl = row.find('img').attr('src'); // Get foto from avatar image

                // Populate edit modal with data
                $('#editKaryawanId').val(nik);
                $('#editNikKaryawan').val(nik);
                $('#editNamaKaryawan').val(nama);
                $('#editDepartemenKaryawan').val(departemen);
                $('#editImageFotoLama').attr('src', fotoUrl);
                $('#editImageFotoPreview').hide();
                
                // Update jabatan dropdown based on departemen
                var jabatanSelect = $('#editJabatanKaryawan');
                jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                if (jabatanByDepartemen[departemen]) {
                    jabatanByDepartemen[departemen].forEach(function(jab) {
                        jabatanSelect.append('<option value="' + jab + '">' + jab + '</option>');
                    });
                    jabatanSelect.val(jabatan);
                }
                
                // Set status radio button
                if (statusText === 'Aktif') {
                    $('#editStatusAktif').prop('checked', true);
                } else {
                    $('#editStatusNonAktif').prop('checked', true);
                }

                // Show modal
                $('#modalEditKaryawan').modal('show');
            });

            // Handle Update Karyawan
            $('#btnUpdateKaryawan').on('click', function() {
                var id = $('#editKaryawanId').val();
                var nik = $('#editNikKaryawan').val();
                var nama = $('#editNamaKaryawan').val();
                var departemenId = $('#editDepartemenKaryawan').val();
                var jabatanId = $('#editJabatanKaryawan').val();
                var status = $('input[name="editStatusKaryawan"]:checked').val();

                if (!nik || !nama || !departemenId || !jabatanId) {
                    alert('Semua field harus diisi!');
                    return;
                }

                $.ajax({
                    url: '/api/employees/' + id,
                    type: 'PUT',
                    data: {
                        nik: nik,
                        name: nama,
                        department_id: departemenId,
                        position_id: jabatanId,
                        status: status || 'active'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Data Karyawan berhasil diperbarui!');
                            $('#modalEditKaryawan').modal('hide');
                            tableKaryawan.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan: ';
                        for (let key in errors) {
                            errorMsg += errors[key][0] + '\n';
                        }
                        alert(errorMsg || 'Gagal memperbarui data');
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

            // Update Jabatan dropdown pada edit modal ketika Departemen berubah
            $('#editDepartemenKaryawan').on('change', function() {
                var selectedDept = $(this).val();
                var jabatanSelect = $('#editJabatanKaryawan');
                jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                
                if (selectedDept && jabatanByDepartemen[selectedDept]) {
                    jabatanByDepartemen[selectedDept].forEach(function(jabatan) {
                        jabatanSelect.append('<option value="' + jabatan + '">' + jabatan + '</option>');
                    });
                }
            });

            // Handle Hapus Karyawan button click
            $(document).on('click', '.btn-hapus-karyawan', function() {
                var row = $(this).closest('tr');
                var nik = row.find('td').eq(2).text(); // Column 2: NIK
                var nama = row.find('td').eq(3).text(); // Column 3: Nama
                var departemen = row.find('td').eq(4).text(); // Column 4: Departemen
                var jabatan = row.find('td').eq(5).text(); // Column 5: Jabatan
                var statusHtml = row.find('td').eq(6).html(); // Column 6: Status (as HTML for badge)
                var fotoUrl = row.find('img').attr('src'); // Get foto from avatar image

                // Populate delete confirmation modal
                $('#hapusKaryawanId').val(nik);
                $('#hapusNikKaryawan').text(nik);
                $('#hapusNamaKaryawan').text(nama);
                $('#hapusDepartemenKaryawan').text(departemen);
                $('#hapusJabatanKaryawan').text(jabatan);
                $('#hapusStatusKaryawan').html(statusHtml);
                $('#hapusFotoKaryawan').attr('src', fotoUrl);

                // Show modal
                $('#modalHapusKaryawan').modal('show');
            });

            // Handle Konfirmasi Hapus Karyawan
            $('#btnKonfirmasiHapusKaryawan').on('click', function() {
                var id = $('#hapusKaryawanId').val();
                var nama = $('#hapusNamaKaryawan').text();

                $.ajax({
                    url: '/api/employees/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Karyawan ' + nama + ' berhasil dihapus!');
                            $('#modalHapusKaryawan').modal('hide');
                            tableKaryawan.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus karyawan: ' + (xhr.responseJSON.message || 'Unknown error'));
                    }
                });
            });

            $('#btnSimpanDepartemen').on('click', function() {
                var namaDept = $('#namaDepartemen').val();
                var jumlahKaryawan = $('#jumlahKaryawan').val();

                if (!namaDept) {
                    alert('Nama departemen harus diisi!');
                    return;
                }

                $.ajax({
                    url: '/api/departments',
                    type: 'POST',
                    data: {
                        name: namaDept,
                        employee_count: jumlahKaryawan || 0,
                        status: 'active'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Departemen berhasil ditambahkan!');
                            $('#modalTambahDepartemen').modal('hide');
                            tableDepartemen.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan: ';
                        for (let key in errors) {
                            errorMsg += errors[key][0] + '\n';
                        }
                        alert(errorMsg || 'Gagal menambahkan departemen');
                    }
                });
            });

            // Handle Edit Departemen button click
            $(document).on('click', '.btn-edit-departemen', function() {
                var id = $(this).data('id');
                var departemen = $(this).data('departemen');
                var jumlah = $(this).data('jumlah');

                // Populate modal with data
                $('#editDepartemenId').val(id);
                $('#editNamaDepartemen').val(departemen);
                $('#editJumlahKaryawan').val(jumlah);

                // Show modal
                $('#modalEditDepartemen').modal('show');
            });

            // Handle Update Departemen
            $('#btnUpdateDepartemen').on('click', function() {
                var id = $('#editDepartemenId').val();
                var namaDept = $('#editNamaDepartemen').val();
                var jumlahKaryawan = $('#editJumlahKaryawan').val();

                if (!namaDept) {
                    alert('Nama departemen harus diisi!');
                    return;
                }

                $.ajax({
                    url: '/api/departments/' + id,
                    type: 'PUT',
                    data: {
                        name: namaDept,
                        employee_count: jumlahKaryawan || 0,
                        status: 'active'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Departemen berhasil diperbarui!');
                            $('#modalEditDepartemen').modal('hide');
                            tableDepartemen.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors || {};
                        var errorMsg = 'Terjadi kesalahan: ';
                        for (let key in errors) {
                            errorMsg += errors[key][0] + '\n';
                        }
                        alert(errorMsg || 'Gagal memperbarui departemen');
                    }
                });
            });

            // Handle Hapus Departemen button click
            $(document).on('click', '.btn-hapus-departemen', function() {
                var id = $(this).data('id');
                var departemen = $(this).data('departemen');

                // Populate modal with data
                $('#hapusDepartemenId').val(id);
                $('#hapusDepartemenNama').text(departemen);

                // Show modal
                $('#modalHapusDepartemen').modal('show');
            });

            // Handle Konfirmasi Hapus
            $('#btnKonfirmasiHapus').on('click', function() {
                var id = $('#hapusDepartemenId').val();
                var departemen = $('#hapusDepartemenNama').text();

                $.ajax({
                    url: '/api/departments/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Departemen ' + departemen + ' berhasil dihapus!');
                            $('#modalHapusDepartemen').modal('hide');
                            tableDepartemen.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus departemen: ' + (xhr.responseJSON.message || 'Unknown error'));
                    }
                });
            });
        });
    </script>
@endpush
