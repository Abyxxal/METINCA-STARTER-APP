<!-- Edit Employee Modal (Modal-LG) -->
<div class="modal fade" id="modalEditEmployee" tabindex="-1" aria-labelledby="editEmployeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editEmployeeLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Data Karyawan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="padding: 2rem;">
                <form id="formEditEmployee">
                    <!-- 2-Column Layout Grid -->
                    <div class="row g-4">
                        <!-- LEFT COLUMN -->
                        <div class="col-md-6">
                            <!-- Field: NIK (Read-only) -->
                            <div class="mb-3">
                                <label for="editNIK" class="form-label fw-bold">
                                    <i class="bi bi-hash me-2"></i>NIK
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="editNIK" 
                                    value="E001"
                                    readonly
                                    style="background-color: #e9ecef; cursor: not-allowed;"
                                >
                                <small class="text-muted">Nomor identitas tidak dapat diubah</small>
                            </div>

                            <!-- Field: Nama Lengkap -->
                            <div class="mb-3">
                                <label for="editNamaLengkap" class="form-label fw-bold">
                                    <i class="bi bi-person-fill me-2"></i>Nama Lengkap
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="editNamaLengkap" 
                                    value="Budi Santoso"
                                    placeholder="Masukkan nama lengkap"
                                    required
                                >
                            </div>

                            <!-- Field: Email -->
                            <div class="mb-3">
                                <label for="editEmail" class="form-label fw-bold">
                                    <i class="bi bi-envelope-fill me-2"></i>Email
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="editEmail" 
                                    value="budi@company.com"
                                    placeholder="Masukkan email"
                                    required
                                >
                            </div>

                            <!-- Field: No. Telepon -->
                            <div class="mb-3">
                                <label for="editNoTelepon" class="form-label fw-bold">
                                    <i class="bi bi-telephone-fill me-2"></i>No. Telepon
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="editNoTelepon" 
                                    value="08123456789"
                                    placeholder="Masukkan nomor telepon"
                                >
                            </div>
                        </div>

                        <!-- RIGHT COLUMN -->
                        <div class="col-md-6">
                            <!-- Field: Departemen -->
                            <div class="mb-3">
                                <label for="editDepartemen" class="form-label fw-bold">
                                    <i class="bi bi-building me-2"></i>Departemen
                                </label>
                                <select class="form-select" id="editDepartemen" required>
                                    <option value="">-- Pilih Departemen --</option>
                                    <option value="IT" selected>Information Technology</option>
                                    <option value="HRD">Human Resources & Development</option>
                                    <option value="Finance">Finance & Accounting</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>

                            <!-- Field: Divisi -->
                            <div class="mb-3">
                                <label for="editDivisi" class="form-label fw-bold">
                                    <i class="bi bi-diagram-3 me-2"></i>Divisi
                                </label>
                                <select class="form-select" id="editDivisi" required>
                                    <option value="">-- Pilih Divisi --</option>
                                    <option value="Backend" selected>Backend Developer</option>
                                    <option value="Frontend">Frontend Developer</option>
                                    <option value="DevOps">DevOps</option>
                                    <option value="QA">Quality Assurance</option>
                                </select>
                            </div>

                            <!-- Field: Jabatan -->
                            <div class="mb-3">
                                <label for="editJabatan" class="form-label fw-bold">
                                    <i class="bi bi-briefcase-fill me-2"></i>Jabatan
                                </label>
                                <select class="form-select" id="editJabatan" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Staff" selected>Staff</option>
                                    <option value="Senior">Senior Engineer</option>
                                    <option value="Lead">Lead Developer</option>
                                    <option value="Head">Head of Department</option>
                                </select>
                            </div>

                            <!-- Field: Status Karyawan (NEW) -->
                            <div class="mb-3">
                                <label for="editStatus" class="form-label fw-bold">
                                    <i class="bi bi-check-circle-fill me-2"></i>Status Karyawan
                                </label>
                                <select class="form-select" id="editStatus" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Aktif" selected style="color: #198754; font-weight: bold;">✓ Aktif</option>
                                    <option value="NonAktif" style="color: #dc3545; font-weight: bold;">✗ Non-Aktif</option>
                                    <option value="Cuti" style="color: #fd7e14; font-weight: bold;">⏸ Cuti</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4">

                    <!-- Additional Info (Read-only) -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted d-block mb-1">Tanggal Bergabung</small>
                                <strong class="text-dark">15 Januari 2023</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted d-block mb-1">Terakhir Diubah</small>
                                <strong class="text-dark">08 Januari 2026 - 14:30</strong>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-warning text-dark fw-bold" id="btnSimpanPerubahanKaryawan">
                    <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnSimpan = document.getElementById('btnSimpanPerubahanKaryawan');
        
        if (btnSimpan) {
            btnSimpan.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get form data
                const formData = {
                    nik: document.getElementById('editNIK').value,
                    nama: document.getElementById('editNamaLengkap').value,
                    email: document.getElementById('editEmail').value,
                    telepon: document.getElementById('editNoTelepon').value,
                    departemen: document.getElementById('editDepartemen').value,
                    divisi: document.getElementById('editDivisi').value,
                    jabatan: document.getElementById('editJabatan').value,
                    status: document.getElementById('editStatus').value
                };
                
                console.log('Edit Karyawan Data:', formData);
                alert('Data karyawan siap diperbarui:\n\n' + JSON.stringify(formData, null, 2));
                
                // TODO: Send to API endpoint (PUT /api/employees/{nik})
            });
        }

        // Handle department change to update divisions
        const deptSelect = document.getElementById('editDepartemen');
        if (deptSelect) {
            deptSelect.addEventListener('change', function() {
                console.log('Department changed to:', this.value);
                // TODO: Load divisions based on selected department
            });
        }

        // Handle division change to update positions
        const divisiSelect = document.getElementById('editDivisi');
        if (divisiSelect) {
            divisiSelect.addEventListener('change', function() {
                console.log('Division changed to:', this.value);
                // TODO: Load positions based on selected division
            });
        }
    });
</script>
