{{-- Modal: Create/Add Employee --}}
<div class="modal fade" id="modalCreateEmployee" tabindex="-1" aria-labelledby="modalCreateEmployeeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            
            {{-- Modal Header --}}
            <div class="modal-header" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%); border: none; color: white;">
                <h5 class="modal-title fw-bold" id="modalCreateEmployeeLabel">
                    <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4">
                <form id="formCreateEmployee" enctype="multipart/form-data" novalidate>
                    
                    {{-- Two Column Layout --}}
                    <div class="row">
                        {{-- LEFT COLUMN: Account & Personal Info --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3" style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-user me-2"></i>Data Pribadi
                            </h6>

                            {{-- NIK --}}
                            <div class="mb-3">
                                <label for="nikInput" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    NIK <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    id="nikInput" 
                                    name="nik" 
                                    placeholder="Contoh: E001" 
                                    required
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                                <small class="text-muted d-block mt-1">Nomor identitas karyawan unik</small>
                            </div>

                            {{-- Full Name --}}
                            <div class="mb-3">
                                <label for="namaInput" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-sm" 
                                    id="namaInput" 
                                    name="nama_lengkap" 
                                    placeholder="Masukkan nama lengkap" 
                                    required
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                            </div>

                            {{-- Email Address --}}
                            <div class="mb-3">
                                <label for="emailInput" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    class="form-control form-control-sm" 
                                    id="emailInput" 
                                    name="email" 
                                    placeholder="nama@company.com" 
                                    required
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                            </div>

                            {{-- Default Password --}}
                            <div class="mb-3">
                                <label for="passwordInput" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Password Default <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    class="form-control form-control-sm" 
                                    id="passwordInput" 
                                    name="password" 
                                    placeholder="Masukkan password" 
                                    value="12345678"
                                    readonly
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem; background-color: #f8f9fa;">
                                <small class="text-muted d-block mt-1">Default: <strong>12345678</strong> (Karyawan dapat mengubahnya setelah login pertama)</small>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Employment Data --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3" style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-briefcase me-2"></i>Data Pekerjaan
                            </h6>

                            {{-- Department --}}
                            <div class="mb-3">
                                <label for="departemenSelect" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Departemen <span class="text-danger">*</span>
                                </label>
                                <select 
                                    class="form-select form-select-sm" 
                                    id="departemenSelect" 
                                    name="departemen" 
                                    required
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                                    <option value="">-- Pilih Departemen --</option>
                                    <!-- Options loaded dynamically from database -->
                                </select>
                            </div>

                            {{-- Division --}}
                            <div class="mb-3">
                                <label for="divisiSelect" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Divisi <span class="text-danger">*</span>
                                </label>
                                <select 
                                    class="form-select form-select-sm" 
                                    id="divisiSelect" 
                                    name="divisi" 
                                    required
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                                    <option value="">-- Pilih Divisi --</option>
                                    <!-- Options loaded based on department selection -->
                                </select>
                            </div>

                            {{-- Position --}}
                            <div class="mb-3">
                                <label for="posisiSelect" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Jabatan <span class="text-danger">*</span>
                                </label>
                                <select 
                                    class="form-select form-select-sm" 
                                    id="posisiSelect" 
                                    name="posisi" 
                                    required
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                                    <option value="">-- Pilih Jabatan --</option>
                                    <!-- Options loaded based on division selection -->
                                </select>
                            </div>

                            {{-- Join Date --}}
                            <div class="mb-3">
                                <label for="joinDateInput" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Tanggal Bergabung <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    class="form-control form-control-sm" 
                                    id="joinDateInput" 
                                    name="join_date" 
                                    required
                                    style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                            </div>

                            {{-- Upload Photo --}}
                            <div class="mb-3">
                                <label for="photoInput" class="form-label fw-600" style="font-size: 0.85rem; font-weight: 600; color: #495057;">
                                    Foto Profil
                                </label>
                                <div class="input-group input-group-sm">
                                    <input 
                                        type="file" 
                                        class="form-control form-control-sm" 
                                        id="photoInput" 
                                        name="photo" 
                                        accept="image/*"
                                        style="border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.625rem 0.875rem; font-size: 0.9rem;">
                                </div>
                                <small class="text-muted d-block mt-1">Format: JPG, PNG (Max: 2MB)</small>
                                {{-- Photo Preview --}}
                                <div id="photoPreview" class="mt-3" style="display: none;">
                                    <img id="previewImage" src="" alt="Photo Preview" style="max-width: 100%; max-height: 150px; border-radius: 0.375rem; border: 1px solid #dee2e6;">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- End of Two Column Layout --}}

                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                <button 
                    type="button" 
                    class="btn btn-sm btn-secondary" 
                    data-bs-dismiss="modal"
                    style="padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem;">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button 
                    type="submit" 
                    form="formCreateEmployee"
                    class="btn btn-sm" 
                    style="padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; background-color: #0d6efd; border-color: #0d6efd; color: white;"
                    onmouseover="this.style.backgroundColor='#0b5ed7'; this.style.borderColor='#0a58ca';"
                    onmouseout="this.style.backgroundColor='#0d6efd'; this.style.borderColor='#0d6efd';">
                    <i class="fas fa-save me-2"></i>Simpan Data
                </button>
            </div>

        </div>
    </div>
</div>

{{-- JavaScript for Photo Preview --}}
<script>
    document.getElementById('photoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('photoPreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('photoPreview').style.display = 'none';
        }
    });

    document.getElementById('formCreateEmployee').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic form validation
        if (this.checkValidity() === false) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        // Collect form data
        const formData = {
            nik: document.getElementById('nikInput').value,
            name: document.getElementById('namaInput').value,
            email: document.getElementById('emailInput').value,
            password: document.getElementById('passwordInput').value,
            department_id: document.getElementById('departemenSelect').value,
            division_id: document.getElementById('divisiSelect').value,
            position_id: document.getElementById('posisiSelect').value,
            join_date: document.getElementById('joinDateInput').value,
            status: 'Aktif'
        };

        // Send to backend
        fetch('/api/employees', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Karyawan berhasil ditambahkan!');
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('modalCreateEmployee')).hide();
                // Reset form
                this.reset();
                document.getElementById('photoPreview').style.display = 'none';
                // Reload page or refresh table
                location.reload();
            } else {
                alert('Gagal menambahkan karyawan: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data');
        });
    });
    
    // Load departments on modal show
    const createModal = document.getElementById('modalCreateEmployee');
    if (createModal) {
        createModal.addEventListener('show.bs.modal', function() {
            loadDepartmentsForCreate();
        });
    }
    
    // Handle dropdown changes
    document.getElementById('departemenSelect').addEventListener('change', function() {
        const deptId = this.value;
        const divisiSelect = document.getElementById('divisiSelect');
        const posisiSelect = document.getElementById('posisiSelect');
        
        divisiSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';
        posisiSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
        
        if (deptId) {
            loadDivisionsForCreate(deptId);
        }
    });
    
    document.getElementById('divisiSelect').addEventListener('change', function() {
        const divId = this.value;
        const posisiSelect = document.getElementById('posisiSelect');
        posisiSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
        
        if (divId) {
            loadPositionsForCreate(divId);
        }
    });
    
    // Load departments function
    async function loadDepartmentsForCreate() {
        try {
            const response = await fetch('/api/dropdowns/departments');
            const result = await response.json();
            
            if (result.success && result.data) {
                const select = document.getElementById('departemenSelect');
                // Clear existing options except the first one
                select.innerHTML = '<option value="">-- Pilih Departemen --</option>';
                
                result.data.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Load departments error:', error);
        }
    }
    
    // Load divisions function
    async function loadDivisionsForCreate(departmentId) {
        try {
            const response = await fetch(`/api/dropdowns/divisions?department_id=${departmentId}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                const select = document.getElementById('divisiSelect');
                result.data.forEach(div => {
                    const option = document.createElement('option');
                    option.value = div.id;
                    option.textContent = div.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Load divisions error:', error);
        }
    }
    
    // Load positions function
    async function loadPositionsForCreate(divisionId) {
        try {
            const response = await fetch(`/api/dropdowns/positions?division_id=${divisionId}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                const select = document.getElementById('posisiSelect');
                result.data.forEach(pos => {
                    const option = document.createElement('option');
                    option.value = pos.id;
                    option.textContent = pos.name;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Load positions error:', error);
        }
    }
</script>
