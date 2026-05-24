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
                                    value=""
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
                                    value=""
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
                                    value=""
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
                                    value=""
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
                                    <!-- Options loaded dynamically from database -->
                                </select>
                            </div>

                            <!-- Field: Divisi -->
                            <div class="mb-3">
                                <label for="editDivisi" class="form-label fw-bold">
                                    <i class="bi bi-diagram-3 me-2"></i>Divisi
                                </label>
                                <select class="form-select" id="editDivisi" required>
                                    <option value="">-- Pilih Divisi --</option>
                                    <!-- Options loaded based on department selection -->
                                </select>
                            </div>

                            <!-- Field: Jabatan -->
                            <div class="mb-3">
                                <label for="editJabatan" class="form-label fw-bold">
                                    <i class="bi bi-briefcase-fill me-2"></i>Jabatan
                                </label>
                                <select class="form-select" id="editJabatan" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <!-- Options loaded based on division selection -->
                                </select>
                            </div>

                            <!-- Field: Status Karyawan (NEW) -->
                            <div class="mb-3">
                                <label for="editStatus" class="form-label fw-bold">
                                    <i class="bi bi-check-circle-fill me-2"></i>Status Karyawan
                                </label>
                                <select class="form-select" id="editStatus" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Aktif" style="color: #198754; font-weight: bold;">✓ Aktif</option>
                                    <option value="Non-Aktif" style="color: #dc3545; font-weight: bold;">✗ Non-Aktif</option>
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
                                <strong class="text-dark" id="editJoinDate">-</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted d-block mb-1">Terakhir Diubah</small>
                                <strong class="text-dark" id="editLastUpdated">-</strong>
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
        const editModal = document.getElementById('modalEditEmployee');
        const form = document.getElementById('formEditEmployee');
        const btnSimpan = document.getElementById('btnSimpanPerubahanKaryawan');
        
        let currentEmployeeNik = null;
        
        // Handle modal show event - load employee data
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (button) {
                    const employeeNik = button.getAttribute('data-employee-nik');
                    if (employeeNik) {
                        loadEmployeeData(employeeNik);
                    }
                }
            });
        }
        
        // Load employee data from backend
        async function loadEmployeeData(nik) {
            try {
                showLoading(true);
                currentEmployeeNik = nik;
                
                const response = await fetch(`/api/employees/${nik}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    populateForm(result.data);
                    await Promise.all([
                        loadDepartments(result.data.department_id),
                        loadDivisions(result.data.division_id, result.data.department_id),
                        loadPositions(result.data.position_id, result.data.division_id)
                    ]);
                } else {
                    showAlert('Error', 'Gagal mengambil data karyawan', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error', 'Terjadi kesalahan saat mengambil data', 'error');
            } finally {
                showLoading(false);
            }
        }
        
        // Populate form dengan data employee
        function populateForm(employee) {
            document.getElementById('editNIK').value = employee.nik || '';
            document.getElementById('editNamaLengkap').value = employee.name || '';
            document.getElementById('editEmail').value = employee.email || '';
            document.getElementById('editNoTelepon').value = employee.phone || '';
            document.getElementById('editStatus').value = employee.status || '';
            
            // Format dates
            if (employee.join_date) {
                const joinDate = new Date(employee.join_date).toLocaleDateString('id-ID', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
                document.getElementById('editJoinDate').textContent = joinDate;
            }
            
            if (employee.updated_at) {
                const updatedAt = new Date(employee.updated_at).toLocaleDateString('id-ID', {
                    year: 'numeric', month: 'long', day: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
                document.getElementById('editLastUpdated').textContent = updatedAt;
            }
        }
        
        // Load dropdowns dengan backend data
        async function loadDepartments(selectedId = null) {
            try {
                const response = await fetch('/api/dropdowns/departments');
                const result = await response.json();
                
                const select = document.getElementById('editDepartemen');
                select.innerHTML = '<option value="">-- Pilih Departemen --</option>';
                
                if (result.success && result.data) {
                    result.data.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        if (selectedId && dept.id == selectedId) option.selected = true;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Load departments error:', error);
            }
        }
        
        async function loadDivisions(selectedId = null, departmentId = null) {
            try {
                const url = '/api/dropdowns/divisions' + (departmentId ? `?department_id=${departmentId}` : '');
                const response = await fetch(url);
                const result = await response.json();
                
                const select = document.getElementById('editDivisi');
                select.innerHTML = '<option value="">-- Pilih Divisi --</option>';
                
                if (result.success && result.data) {
                    result.data.forEach(div => {
                        const option = document.createElement('option');
                        option.value = div.id;
                        option.textContent = div.name;
                        if (selectedId && div.id == selectedId) option.selected = true;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Load divisions error:', error);
            }
        }
        
        async function loadPositions(selectedId = null, divisionId = null) {
            try {
                if (!divisionId) return;
                
                const response = await fetch(`/api/dropdowns/positions?division_id=${divisionId}`);
                const result = await response.json();
                
                const select = document.getElementById('editJabatan');
                select.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                
                if (result.success && result.data) {
                    result.data.forEach(pos => {
                        const option = document.createElement('option');
                        option.value = pos.id;
                        option.textContent = pos.name;
                        if (selectedId && pos.id == selectedId) option.selected = true;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Load positions error:', error);
            }
        }
        
        // Handle cascade dropdowns
        const deptSelect = document.getElementById('editDepartemen');
        if (deptSelect) {
            deptSelect.addEventListener('change', function() {
                const departmentId = this.value;
                document.getElementById('editDivisi').innerHTML = '<option value="">-- Pilih Divisi --</option>';
                document.getElementById('editJabatan').innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                if (departmentId) loadDivisions(null, departmentId);
            });
        }
        
        const divisiSelect = document.getElementById('editDivisi');
        if (divisiSelect) {
            divisiSelect.addEventListener('change', function() {
                const divisionId = this.value;
                document.getElementById('editJabatan').innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                if (divisionId) loadPositions(null, divisionId);
            });
        }
        
        // Handle form submission
        if (btnSimpan) {
            btnSimpan.addEventListener('click', async function(e) {
                e.preventDefault();
                
                if (!currentEmployeeNik) {
                    showAlert('Error', 'Data karyawan tidak ditemukan', 'error');
                    return;
                }
                
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                try {
                    showLoading(true);
                    
                    const formData = {
                        nik: document.getElementById('editNIK').value,
                        name: document.getElementById('editNamaLengkap').value,
                        email: document.getElementById('editEmail').value,
                        phone: document.getElementById('editNoTelepon').value || null,
                        department_id: document.getElementById('editDepartemen').value,
                        division_id: document.getElementById('editDivisi').value,
                        position_id: document.getElementById('editJabatan').value,
                        status: document.getElementById('editStatus').value
                    };
                    
                    const response = await fetch(`/api/employees/${currentEmployeeNik}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(formData)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showAlert('Berhasil', 'Data karyawan berhasil diperbarui', 'success');
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(editModal);
                        modal.hide();
                        
                        // Refresh data table
                        if (window.employeeTable && typeof window.employeeTable.ajax !== 'undefined') {
                            window.employeeTable.ajax.reload(null, false);
                        } else {
                            setTimeout(() => location.reload(), 1500);
                        }
                    } else {
                        showAlert('Error', result.message || 'Gagal memperbarui data', 'error');
                    }
                } catch (error) {
                    console.error('Update error:', error);
                    showAlert('Error', 'Terjadi kesalahan saat memperbarui data', 'error');
                } finally {
                    showLoading(false);
                }
            });
        }
        
        // Helper functions
        function showLoading(show) {
            if (btnSimpan) {
                if (show) {
                    btnSimpan.disabled = true;
                    btnSimpan.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                } else {
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = '<i class="bi bi-check-circle me-2"></i>Simpan Perubahan';
                }
            }
        }
        
        function showAlert(title, message, type) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: message,
                    confirmButtonText: 'OK'
                });
            } else {
                alert(title + ': ' + message);
            }
        }
    });
</script>
