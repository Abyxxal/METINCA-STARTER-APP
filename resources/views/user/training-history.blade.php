@extends('layouts.app-user')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3">Riwayat Pelatihan</h1>
            <p class="text-muted">Daftar pelatihan yang telah Anda selesaikan</p>
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
                                <option value="">Semua Tahun</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option value="">Semua Kategori</option>
                                <option value="safety">Safety</option>
                                <option value="quality">Quality</option>
                                <option value="operation">Operation</option>
                                <option value="management">Management</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training History List -->
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
                                <th>Tanggal Selesai</th>
                                <th>Nilai / Score</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><strong>Machine Operation - Advanced</strong></td>
                                <td><span class="badge bg-success">Operation</span></td>
                                <td><span class="badge bg-danger">Level 3</span></td>
                                <td>15 Nov 2025</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-2">92</span>
                                        <div class="progress" style="width: 100px; height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 92%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download Sertifikat">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><strong>Safety Training - Level 2</strong></td>
                                <td><span class="badge bg-secondary">Safety</span></td>
                                <td><span class="badge bg-warning">Level 2</span></td>
                                <td>10 Oct 2025</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-2">88</span>
                                        <div class="progress" style="width: 100px; height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 88%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download Sertifikat">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><strong>Quality Control Basics</strong></td>
                                <td><span class="badge bg-primary">Quality</span></td>
                                <td><span class="badge bg-info">Level 1</span></td>
                                <td>25 Sep 2025</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-2">85</span>
                                        <div class="progress" style="width: 100px; height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 85%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download Sertifikat">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><strong>ISO 9001:2015 Introduction</strong></td>
                                <td><span class="badge bg-warning text-dark">Management</span></td>
                                <td><span class="badge bg-info">Level 1</span></td>
                                <td>15 Aug 2025</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-2">90</span>
                                        <div class="progress" style="width: 100px; height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 90%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download Sertifikat">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td><strong>Maintenance Best Practices</strong></td>
                                <td><span class="badge bg-info">Maintenance</span></td>
                                <td><span class="badge bg-warning">Level 2</span></td>
                                <td>05 Jul 2025</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-2">87</span>
                                        <div class="progress" style="width: 100px; height: 6px;">
                                            <div class="progress-bar bg-success" style="width: 87%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download Sertifikat">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
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
