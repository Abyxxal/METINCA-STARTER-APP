@extends('layouts.app')

@section('title', 'Detail Departemen')

@push('styles')
<style>
    .card.border-dashed {
        border: 2px dashed var(--bs-border-color) !important;
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Departemen</h3>
                <p class="text-subtitle text-muted">Struktur divisi dan jabatan pada departemen ini</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('master-data') }}">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Departemen</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    {{-- Main Content Section --}}
    <div class="row g-4">
        {{-- Division Cards Grid --}}
        <div class="col-lg-9">
            <div class="d-flex align-items-center gap-2 mb-3">
                <a href="{{ route('master-data') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-chevron-left me-1"></i>Kembali
                </a>
                <span class="badge bg-primary">Information Technology</span>
            </div>
            <div class="row g-3">
                {{-- Division Card 1: Backend --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        {{-- Card Header --}}
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-diagram-3 me-2 text-primary"></i>Backend Developer
                            </h6>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-warning" title="Edit Divisi">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Divisi">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body p-4">
                            {{-- Positions List Title --}}
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-list-check me-2"></i>Daftar Jabatan:
                            </h6>

                            {{-- Positions List --}}
                            <div class="list-group list-group-sm">
                                {{-- Position 1 --}}
                                <div class="list-group-item border rounded mb-2 p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold small">Head of Backend</span>
                                            <br>
                                            <small class="text-muted">1 Karyawan</small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Position 2 --}}
                                <div class="list-group-item border rounded mb-2 p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold small">Senior Backend Engineer</span>
                                            <br>
                                            <small class="text-muted">2 Karyawan</small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Position 3 --}}
                                <div class="list-group-item border rounded p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold small">Junior Backend Developer</span>
                                            <br>
                                            <small class="text-muted">1 Karyawan</small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Add Position Button --}}
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-3" data-division="Backend Developer">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Jabatan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Division Card 2: Frontend --}}
                <div class="col-md-6">
                    <div class="card h-100">
                        {{-- Card Header --}}
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-diagram-3 me-2 text-primary"></i>Frontend Developer
                            </h6>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-warning" title="Edit Divisi">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" title="Hapus Divisi">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body p-4">
                            {{-- Positions List Title --}}
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-list-check me-2"></i>Daftar Jabatan:
                            </h6>

                            {{-- Positions List --}}
                            <div class="list-group list-group-sm">
                                {{-- Position 1 --}}
                                <div class="list-group-item border rounded mb-2 p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold small">Head of Frontend</span>
                                            <br>
                                            <small class="text-muted">1 Karyawan</small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Position 2 --}}
                                <div class="list-group-item border rounded mb-2 p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold small">Senior Frontend Engineer</span>
                                            <br>
                                            <small class="text-muted">3 Karyawan</small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Position 3 --}}
                                <div class="list-group-item border rounded p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold small">Junior Frontend Developer</span>
                                            <br>
                                            <small class="text-muted">2 Karyawan</small>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Add Position Button --}}
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 mt-3" data-division="Frontend Developer">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Jabatan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Add Division Card --}}
                <div class="col-md-6">
                    <div class="card border-dashed h-100 d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <div class="card-body text-center">
                            <i class="bi bi-plus-lg text-primary" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold mt-3 mb-3">Tambah Divisi Baru</h6>
                            <p class="text-muted small mb-3">Klik tombol di bawah untuk menambahkan divisi baru ke departemen ini</p>
                            <button type="button" class="btn btn-primary btn-sm" id="btnTambahDivisi">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Divisi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Department Summary --}}
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>Ringkasan Departemen
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Stat 1: Total Divisions --}}
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Total Divisi</div>
                        <div class="fw-bold fs-3 text-primary">2</div>
                    </div>

                    {{-- Stat 2: Total Positions --}}
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Total Jabatan</div>
                        <div class="fw-bold fs-3 text-success">6</div>
                    </div>

                    {{-- Stat 3: Total Employees --}}
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Total Karyawan</div>
                        <div class="fw-bold fs-3 text-warning">9</div>
                    </div>

                    <hr>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditDepartment">
                            <i class="bi bi-pencil-square me-2"></i>Edit Departemen
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-2"></i>Hapus Departemen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include Edit Department Modal --}}
@include('admin.departments.modals.edit-department')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle add position button
        document.querySelectorAll('button[data-division]').forEach(btn => {
            btn.addEventListener('click', function() {
                const divisionName = this.getAttribute('data-division');
                Swal.fire({
                    icon: 'info',
                    title: 'Tambah Jabatan',
                    text: 'Form tambah jabatan untuk divisi: ' + divisionName
                });
            });
        });

        // Handle add division button
        const btnTambahDivisi = document.getElementById('btnTambahDivisi');
        if (btnTambahDivisi) {
            btnTambahDivisi.addEventListener('click', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Tambah Divisi',
                    text: 'Form tambah divisi baru akan dibuka'
                });
            });
        }
    });
</script>
@endpush
