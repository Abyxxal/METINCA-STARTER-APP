<!-- Edit Department Modal (Modal-LG) -->
<div class="modal fade" id="modalEditDepartment" tabindex="-1" aria-labelledby="editDepartmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Data Departemen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="formEditDepartment">
                    <!-- Section 1: Department Info -->
                    <div class="mb-4">
                        <label for="inputEditNamaDepartemen" class="form-label fw-bold mb-2">
                            <i class="bi bi-building me-2"></i>Nama Departemen
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="inputEditNamaDepartemen" 
                            placeholder="Masukkan nama departemen"
                        >
                    </div>

                    <hr class="my-4">

                    <!-- Section 2: Divisions Grid -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">
                            <i class="bi bi-diagram-3 me-2"></i>Struktur Divisi & Jabatan
                        </label>

                        <!-- Container for Division Cards Grid -->
                        <div id="containerDivisions" class="row g-4 mb-4">
                            <!-- Division Card 1: Backend -->
                            <div class="col-md-6 division-card" data-division-id="1">
                                <div class="card h-100">
                                    <!-- Division Header -->
                                    <div class="card-header">
                                        <h6 class="card-title mb-0 fw-bold">
                                            <i class="bi bi-diagram-3 me-2 text-primary"></i>Backend Developer
                                        </h6>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-warning delete-division-btn" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger delete-division-btn" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Division Body -->
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-4">
                                            <i class="bi bi-list-check me-2"></i>Daftar Jabatan:
                                        </h6>

                                        <!-- Container for Positions -->
                                        <div class="positions-container mb-4">
                                            <!-- Position 1 -->
                                            <div class="position-row mb-3 p-3 border-start border-4 border-primary bg-body-tertiary rounded-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold position-name-input">Head of Backend</div>
                                                        <small class="text-muted">1 Karyawan</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm ms-2" role="group">
                                                        <button type="button" class="btn btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger delete-position-btn" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Position 2 -->
                                            <div class="position-row mb-3 p-3 border-start border-4 border-primary bg-body-tertiary rounded-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold position-name-input">Senior Backend Engineer</div>
                                                        <small class="text-muted">2 Karyawan</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm ms-2" role="group">
                                                        <button type="button" class="btn btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger delete-position-btn" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Add Position Button -->
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-primary w-100 add-position-btn"
                                        >
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Jabatan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Division Card 2: Frontend -->
                            <div class="col-md-6 division-card" data-division-id="2">
                                <div class="card h-100">
                                    <!-- Division Header -->
                                    <div class="card-header">
                                        <h6 class="card-title mb-0 fw-bold">
                                            <i class="bi bi-diagram-3 me-2 text-primary"></i>Frontend Developer
                                        </h6>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-warning delete-division-btn" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger delete-division-btn" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Division Body -->
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-4">
                                            <i class="bi bi-list-check me-2"></i>Daftar Jabatan:
                                        </h6>

                                        <!-- Container for Positions -->
                                        <div class="positions-container mb-4">
                                            <!-- Position 1 -->
                                            <div class="position-row mb-3 p-3 border-start border-4 border-primary bg-body-tertiary rounded-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold position-name-input">Head of Frontend</div>
                                                        <small class="text-muted">1 Karyawan</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm ms-2" role="group">
                                                        <button type="button" class="btn btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger delete-position-btn" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Position 2 -->
                                            <div class="position-row mb-3 p-3 border-start border-4 border-primary bg-body-tertiary rounded-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold position-name-input">UI/UX Designer</div>
                                                        <small class="text-muted">1 Karyawan</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm ms-2" role="group">
                                                        <button type="button" class="btn btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger delete-position-btn" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Position 3 -->
                                            <div class="position-row mb-3 p-3 border-start border-4 border-primary bg-body-tertiary rounded-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold position-name-input">Senior Frontend Engineer</div>
                                                        <small class="text-muted">3 Karyawan</small>
                                                    </div>
                                                    <div class="btn-group btn-group-sm ms-2" role="group">
                                                        <button type="button" class="btn btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger delete-position-btn" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Add Position Button -->
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-primary w-100 add-position-btn"
                                        >
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Jabatan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add Division Button -->
                        <div class="text-center mt-4">
                            <button 
                                type="button" 
                                class="btn btn-outline-primary btn-sm px-4 py-2" 
                                id="btnTambahDivisiEdit"
                            >
                                <i class="bi bi-plus-lg me-2"></i>Tambah Divisi Baru
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSimpanPerubahan">
                    <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEditDept = document.getElementById('modalEditDepartment');
        
        if (modalEditDept) {
            // ========== DELETE POSITION ==========
            modalEditDept.addEventListener('click', function(e) {
                if (e.target.closest('.delete-position-btn')) {
                    e.preventDefault();
                    const positionRow = e.target.closest('.position-row');
                    if (positionRow) {
                        positionRow.remove();
                    }
                }
            });

            // ========== ADD POSITION ==========
            modalEditDept.addEventListener('click', function(e) {
                if (e.target.closest('.add-position-btn')) {
                    e.preventDefault();
                    const divisionCard = e.target.closest('.division-card');
                    if (divisionCard) {
                        const positionsContainer = divisionCard.querySelector('.positions-container');
                        if (positionsContainer) {
                            // Create new position row
                            const newPositionRow = document.createElement('div');
                            newPositionRow.className = 'input-group input-group-sm mb-2 position-row';
                            newPositionRow.innerHTML = `
                                <input 
                                    type="text" 
                                    class="form-control position-name-input" 
                                    placeholder="Nama jabatan"
                                >
                                <button type="button" class="btn btn-outline-danger delete-position-btn" title="Hapus Jabatan">
                                    <i class="bi bi-x"></i>
                                </button>
                            `;
                            positionsContainer.appendChild(newPositionRow);
                            // Focus on the new input
                            newPositionRow.querySelector('.position-name-input').focus();
                        }
                    }
                }
            });

            // ========== DELETE DIVISION ==========
            modalEditDept.addEventListener('click', function(e) {
                if (e.target.closest('.delete-division-btn')) {
                    e.preventDefault();
                    const divisionCard = e.target.closest('.division-card');
                    if (divisionCard) {
                        // Confirm deletion
                        Swal.fire({
                            icon: 'warning',
                            title: 'Hapus divisi ini?',
                            text: 'Divisi beserta seluruh jabatannya akan dihapus dari form',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: SWAL_BTN.danger
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                divisionCard.remove();
                            }
                        });
                    }
                }
            });

            // ========== ADD DIVISION ==========
            const btnTambahDivisiEdit = document.getElementById('btnTambahDivisiEdit');
            if (btnTambahDivisiEdit) {
                btnTambahDivisiEdit.addEventListener('click', function(e) {
                    e.preventDefault();
                    const containerDivisions = document.getElementById('containerDivisions');
                    if (containerDivisions) {
                        // Generate unique ID for new division
                        const divisionId = Date.now();
                        
                        // Create new division card
                        const newDivisionCard = document.createElement('div');
                        newDivisionCard.className = 'col-md-6 division-card';
                        newDivisionCard.setAttribute('data-division-id', divisionId);
                        newDivisionCard.innerHTML = `
                            <div class="card h-100">
                                <!-- Division Header -->
                                <div class="card-header">
                                    <div class="flex-grow-1">
                                        <input 
                                            type="text" 
                                            class="form-control form-control-sm division-name-input" 
                                            placeholder="Nama divisi"
                                        >
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-division-btn" title="Hapus Divisi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Division Body -->
                                <div class="card-body">
                                    <label class="form-label fw-bold small mb-2">
                                        <i class="bi bi-list-check me-2"></i>Jabatan di divisi ini:
                                    </label>

                                    <!-- Container for Position Inputs -->
                                    <div class="positions-container mb-3">
                                        <!-- Empty initially -->
                                    </div>

                                    <!-- Add Position Button -->
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary add-position-btn w-100"
                                        title="Tambah Jabatan"
                                    >
                                        <i class="bi bi-plus-circle me-2"></i>Tambah Jabatan
                                    </button>
                                </div>
                            </div>
                        `;
                        
                        // Append to the divisions grid
                        containerDivisions.appendChild(newDivisionCard);
                        
                        // Focus on the division name input
                        newDivisionCard.querySelector('.division-name-input').focus();
                    }
                });
            }

            // ========== SAVE CHANGES ==========
            const btnSimpanPerubahan = document.getElementById('btnSimpanPerubahan');
            if (btnSimpanPerubahan) {
                btnSimpanPerubahan.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const departmentName = document.getElementById('inputEditNamaDepartemen').value;
                    
                    // Collect all divisions data
                    const divisions = [];
                    const divisionCards = modalEditDept.querySelectorAll('.division-card');
                    
                    divisionCards.forEach(card => {
                        const nameInput = card.querySelector('.division-name-input');
                        const divisionName = nameInput ? nameInput.value : card.querySelector('.card-title')?.textContent.trim() || '';
                        const positions = [];
                        
                        // Collect positions for this division
                        const positionRows = card.querySelectorAll('.position-row');
                        positionRows.forEach(row => {
                            const positionName = row.querySelector('.position-name-input').value;
                            if (positionName.trim()) {
                                positions.push(positionName);
                            }
                        });
                        
                        if (divisionName.trim()) {
                            divisions.push({
                                name: divisionName,
                                positions: positions
                            });
                        }
                    });
                    
                    // Log or send data
                    const departmentData = {
                        name: departmentName,
                        divisions: divisions
                    };
                    
                    console.log('Department Data:', departmentData);
                    Swal.fire({
                        icon: 'info',
                        title: 'Data departemen siap disimpan',
                        html: '<pre class="text-start small mb-0" style="max-height:300px;overflow:auto;">' +
                            JSON.stringify(departmentData, null, 2).replace(/</g, '&lt;') + '</pre>'
                    });
                    
                    // TODO: Send to API endpoint
                    // POST /api/departments/{id}
                });
            }
        }
    });
</script>
