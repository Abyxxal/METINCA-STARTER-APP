@extends('layouts.app-user')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Pelatihan Saya</h1>
            <p class="text-muted">Daftar pelatihan yang ditugaskan untuk Anda</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" placeholder="Cari pelatihan...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">Dalam Proses</option>
                                <option value="completed">Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option value="">Semua Level</option>
                                <option value="1">Level 1 - Basic</option>
                                <option value="2">Level 2 - Intermediate</option>
                                <option value="3">Level 3 - Advanced</option>
                                <option value="4">Level 4 - Expert</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th>No</th>
                                <th>Nama Pelatihan</th>
                                <th>Kategori</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><strong>Safety Training - Level 1</strong></td>
                                <td><span class="badge bg-secondary">Safety</span></td>
                                <td><span class="badge bg-info">Level 1</span></td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>31 Des 2025</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Mulai Pelatihan">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><strong>Quality Control Basics</strong></td>
                                <td><span class="badge bg-primary">Quality</span></td>
                                <td><span class="badge bg-info">Level 1</span></td>
                                <td><span class="badge bg-info">In Progress</span></td>
                                <td>15 Jan 2026</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Lanjutkan">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><strong>Machine Operation - Advanced</strong></td>
                                <td><span class="badge bg-success">Operation</span></td>
                                <td><span class="badge bg-danger">Level 3</span></td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>20 Jan 2026</td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Lihat Sertifikat">
                                        <i class="fas fa-certificate"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><strong>ISO 9001 Implementation</strong></td>
                                <td><span class="badge bg-warning text-dark">Management</span></td>
                                <td><span class="badge bg-warning">Level 2</span></td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>28 Feb 2026</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Mulai Pelatihan">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td><strong>Emergency Response Team</strong></td>
                                <td><span class="badge bg-danger">Safety</span></td>
                                <td><span class="badge bg-danger">Level 4</span></td>
                                <td><span class="badge bg-info">In Progress</span></td>
                                <td>15 Mar 2026</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Lanjutkan">
                                        <i class="fas fa-play"></i>
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

<script>
    // Enable tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection
