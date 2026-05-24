@extends('layouts.app')

@section('title', 'Detail Departemen')

@section('content')
<div class="container-fluid py-4 px-4">
    {{-- Page Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <a href="{{ route('master-data') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-chevron-left me-1"></i>Kembali
                </a>
                <h4 class="mb-0"><i class="bi bi-building me-2"></i>Detail Departemen</h4>
            </div>
            <p class="text-muted mb-0">
                <span class="badge bg-primary" style="font-size: 0.95rem; padding: 0.5rem 0.75rem;">
                    Information Technology
                </span>
            </p>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="row">
        {{-- Division Cards Grid --}}
        <div class="col-lg-9">
            <div class="row g-3">
                {{-- Division Card 1: Backend --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        {{-- Card Header --}}
                        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-diagram-3 me-2"></i>Backend Developer
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
                    <div class="card border-0 shadow-sm h-100">
                        {{-- Card Header --}}
                        <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="bi bi-diagram-3 me-2"></i>Frontend Developer
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
                    <div class="card border-2 border-dashed shadow-sm h-100 d-flex align-items-center justify-content-center" style="min-height: 400px;">
                        <div class="card-body text-center">
                            <i class="bi bi-plus-lg" style="font-size: 3rem; color: #0d6efd; margin-bottom: 1rem;"></i>
                            <h6 class="fw-bold mb-3">Tambah Divisi Baru</h6>
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>Ringkasan Departemen
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Stat 1: Total Divisions --}}
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Total Divisi</div>
                        <div class="fw-bold" style="font-size: 1.5rem; color: #0d6efd;">2</div>
                    </div>

                    {{-- Stat 2: Total Positions --}}
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Total Jabatan</div>
                        <div class="fw-bold" style="font-size: 1.5rem; color: #20c997;">6</div>
                    </div>

                    {{-- Stat 3: Total Employees --}}
                    <div class="mb-4">
                        <div class="text-muted small mb-1">Total Karyawan</div>
                        <div class="fw-bold" style="font-size: 1.5rem; color: #fd7e14;">9</div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle add position button
        document.querySelectorAll('button[data-division]').forEach(btn => {
            btn.addEventListener('click', function() {
                const divisionName = this.getAttribute('data-division');
                console.log('Tambah Jabatan untuk divisi:', divisionName);
                // Open modal to add position for this division
                alert('Modal untuk tambah jabatan ke divisi: ' + divisionName);
            });
        });

        // Handle add division button
        const btnTambahDivisi = document.getElementById('btnTambahDivisi');
        if (btnTambahDivisi) {
            btnTambahDivisi.addEventListener('click', function() {
                console.log('Tambah Divisi baru');
                // Open modal to add new division
                alert('Modal untuk tambah divisi baru akan dibuka');
            });
        }
    });
</script>
@endsection
