<!-- Department Detail Modal (Modal-LG) -->
<div class="modal fade" id="modalDetailDepartment" tabindex="-1" aria-labelledby="detailDepartmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="detailDepartmentLabel">
                    <i class="bi bi-building me-2"></i>Detail Departemen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Department Title -->
                <div class="mb-4">
                    <h6 class="text-muted small">Departemen</h6>
                    <h4 class="mb-0 fw-bold">
                        <span id="detailDeptName">Information Technology</span>
                    </h4>
                </div>

                <hr class="my-4">

                <!-- Summary Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card bg-body-tertiary text-center border-0">
                            <div class="card-body py-3">
                                <h5 class="mb-1 text-primary fw-bold fs-2">2</h5>
                                <p class="text-muted small mb-0">Total Divisi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-body-tertiary text-center border-0">
                            <div class="card-body py-3">
                                <h5 class="mb-1 text-success fw-bold fs-2">9</h5>
                                <p class="text-muted small mb-0">Total Karyawan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Divisions List -->
                <h6 class="fw-bold mb-4">
                    <i class="bi bi-diagram-3 me-2"></i>Daftar Divisi
                </h6>

                <div class="row g-4" id="divisionsContainer">
                    <!-- Division Card 1: Backend -->
                    <div class="col-md-6">
                        <div class="card division-detail-card h-100">
                            <div class="card-header">
                                <h6 class="card-title mb-0 fw-bold">
                                    <i class="bi bi-diagram-3 me-2 text-primary"></i>Backend Developer
                                </h6>
                            </div>
                            <div class="card-body py-4">
                                <div class="mb-3">
                                    <p class="text-muted small mb-2">Total Karyawan</p>
                                    <span class="badge bg-primary px-3 py-2">4 Karyawan</span>
                                </div>
                                <div>
                                    <p class="text-muted small mb-2">Total Jabatan</p>
                                    <span class="badge bg-secondary px-3 py-2">2 Jabatan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Division Card 2: Frontend -->
                    <div class="col-md-6">
                        <div class="card division-detail-card h-100">
                            <div class="card-header">
                                <h6 class="card-title mb-0 fw-bold">
                                    <i class="bi bi-diagram-3 me-2 text-primary"></i>Frontend Developer
                                </h6>
                            </div>
                            <div class="card-body py-4">
                                <div class="mb-3">
                                    <p class="text-muted small mb-2">Total Karyawan</p>
                                    <span class="badge bg-primary px-3 py-2">5 Karyawan</span>
                                </div>
                                <div>
                                    <p class="text-muted small mb-2">Total Jabatan</p>
                                    <span class="badge bg-secondary px-3 py-2">3 Jabatan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
