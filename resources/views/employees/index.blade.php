@php
    // Dummy Employee Data - For UI Development Only
    $employees = [
        [
            'id' => 1,
            'nik' => 'E001',
            'nama' => 'Budi Santoso',
            'email' => 'budi.santoso@company.com',
            'departemen' => 'IT',
            'divisi' => 'Backend',
            'jabatan' => 'Senior Developer',
            'status' => 'active',
            'foto' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Budi'
        ],
        [
            'id' => 2,
            'nik' => 'E002',
            'nama' => 'Siti Nurhaliza',
            'email' => 'siti.nurhaliza@company.com',
            'departemen' => 'HRD',
            'divisi' => 'Recruitment',
            'jabatan' => 'HR Specialist',
            'status' => 'active',
            'foto' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Siti'
        ],
        [
            'id' => 3,
            'nik' => 'E003',
            'nama' => 'Ahmad Wijaya',
            'email' => 'ahmad.wijaya@company.com',
            'departemen' => 'Finance',
            'divisi' => 'Payroll',
            'jabatan' => 'Finance Manager',
            'status' => 'active',
            'foto' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Ahmad'
        ],
        [
            'id' => 4,
            'nik' => 'E004',
            'nama' => 'Rina Setiawan',
            'email' => 'rina.setiawan@company.com',
            'departemen' => 'IT',
            'divisi' => 'Backend',
            'jabatan' => 'Junior Developer',
            'status' => 'active',
            'foto' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Rina'
        ],
        [
            'id' => 5,
            'nik' => 'E005',
            'nama' => 'Rudi Hermawan',
            'email' => 'rudi.hermawan@company.com',
            'departemen' => 'HRD',
            'divisi' => 'Recruitment',
            'jabatan' => 'Recruiter',
            'status' => 'inactive',
            'foto' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Rudi'
        ],
    ];

    $departments = ['IT', 'HRD', 'Finance'];
    $divisions = ['Backend', 'Recruitment', 'Payroll'];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Karyawan - Employee Management</title>
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- FontAwesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #0d6efd;
            --danger: #dc3545;
            --warning: #ffc107;
            --success: #198754;
            --secondary: #6c757d;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-wrapper {
            padding: 2rem 0;
        }

        .card-header-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            border: none;
            color: white;
            padding: 1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .card-header-custom h4 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toolbar-section {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group > div {
            flex: 0 0 auto;
        }

        .filter-group .search-box {
            margin-left: auto;
            flex: 0 0 250px;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: block;
        }

        .filter-group select,
        .filter-group input {
            font-size: 0.875rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            transition: border-color 0.2s;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        }

        .btn-sm-custom {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary-custom {
            background-color: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
        }

        .btn-secondary-custom {
            background-color: white;
            border: 1px solid var(--secondary);
            color: var(--secondary);
        }

        .btn-secondary-custom:hover {
            background-color: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }

        .table-responsive-custom {
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table-custom thead th {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
            padding: 1rem 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        .table-custom tbody td {
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .foto-cell {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
        }

        .badge-status {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.4rem 0.6rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            min-height: 32px;
        }

        .btn-edit {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }

        .btn-edit:hover {
            background-color: #ffc107;
            color: white;
            transform: scale(1.1);
        }

        .btn-reset {
            background-color: #e2e3e5;
            color: #383d41;
            border-color: #d3d6d8;
        }

        .btn-reset:hover {
            background-color: #6c757d;
            color: white;
            transform: scale(1.1);
        }

        .btn-delete {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #dc3545;
            color: white;
            transform: scale(1.1);
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            border: none;
        }

        .modal-header-custom .btn-close {
            filter: brightness(0) invert(1);
        }

        .form-group-custom {
            margin-bottom: 1.25rem;
        }

        .form-group-custom label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-group-custom input,
        .form-group-custom select {
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.625rem 0.875rem;
            width: 100%;
            transition: border-color 0.2s;
        }

        .form-group-custom input:focus,
        .form-group-custom select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
            outline: none;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .filter-group {
                flex-direction: column;
            }

            .filter-group > div {
                width: 100%;
            }

            .filter-group .search-box {
                margin-left: 0;
                flex: 1 1 auto;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header-title i {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="container-lg">
            {{-- Header Section --}}
            <div class="header-top">
                <div class="header-title">
                    <i class="fas fa-users"></i>
                    Data Karyawan
                </div>
                <button class="btn btn-primary-custom btn-sm-custom" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Karyawan
                </button>
            </div>

            {{-- Toolbar Section --}}
            <div class="toolbar-section">
                <div class="filter-group">
                    <div>
                        <label for="filterDepartemen">Filter Departemen</label>
                        <select id="filterDepartemen" class="form-control">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filterDivisi">Filter Divisi</label>
                        <select id="filterDivisi" class="form-control">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div }}">{{ $div }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button class="btn btn-secondary-custom btn-sm-custom" onclick="resetFilters()">
                            <i class="fas fa-redo me-2"></i>Reset Filter
                        </button>
                    </div>

                    <div class="search-box">
                        <label for="searchInput">Cari Nama/NIK</label>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama atau NIK...">
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="card border-0 shadow-sm">
                <div class="table-responsive-custom">
                    <table class="table table-hover table-custom">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th style="width: 60px;">Foto</th>
                                <th style="width: 80px;">NIK</th>
                                <th>Nama Lengkap</th>
                                <th style="width: 120px;">Departemen</th>
                                <th style="width: 120px;">Divisi</th>
                                <th>Jabatan</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($employees && count($employees) > 0)
                                @foreach($employees as $index => $employee)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                        <td class="text-center">
                                            <img src="{{ $employee['foto'] }}" alt="{{ $employee['nama'] }}" class="foto-cell">
                                        </td>
                                        <td class="fw-bold">{{ $employee['nik'] }}</td>
                                        <td>{{ $employee['nama'] }}</td>
                                        <td>{{ $employee['departemen'] }}</td>
                                        <td>{{ $employee['divisi'] }}</td>
                                        <td>{{ $employee['jabatan'] }}</td>
                                        <td>
                                            @if($employee['status'] === 'active')
                                                <span class="badge-status badge-active">Aktif</span>
                                            @else
                                                <span class="badge-status badge-inactive">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-edit" title="Edit Karyawan">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-action btn-reset" title="Reset Password">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                                <button class="btn-action btn-delete" title="Hapus Karyawan">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p class="mb-0">Tidak ada data karyawan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Tambah Karyawan --}}
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                {{-- Modal Header --}}
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title fw-bold" id="addEmployeeModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4">
                    <form id="addEmployeeForm">
                        {{-- Row 1: NIK & Email --}}
                        <div class="form-row">
                            <div class="form-group-custom">
                                <label for="nikInput">NIK <span class="text-danger">*</span></label>
                                <input type="text" id="nikInput" class="form-control" placeholder="Masukkan NIK" required>
                            </div>
                            <div class="form-group-custom">
                                <label for="emailInput">Email <span class="text-danger">*</span></label>
                                <input type="email" id="emailInput" class="form-control" placeholder="Masukkan Email" required>
                            </div>
                        </div>

                        {{-- Row 2: Nama Lengkap (Full Width) --}}
                        <div class="form-row full">
                            <div class="form-group-custom">
                                <label for="namaInput">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" id="namaInput" class="form-control" placeholder="Masukkan Nama Lengkap" required>
                            </div>
                        </div>

                        {{-- Row 3: Department & Division --}}
                        <div class="form-row">
                            <div class="form-group-custom">
                                <label for="departemenSelect">Departemen <span class="text-danger">*</span></label>
                                <select id="departemenSelect" class="form-control" required>
                                    <option value="">-- Pilih Departemen --</option>
                                    <option value="IT">IT</option>
                                    <option value="HRD">HRD</option>
                                    <option value="Finance">Finance</option>
                                </select>
                            </div>
                            <div class="form-group-custom">
                                <label for="divisiSelect">Divisi <span class="text-danger">*</span></label>
                                <select id="divisiSelect" class="form-control" required>
                                    <option value="">-- Pilih Divisi --</option>
                                    <option value="Backend">Backend</option>
                                    <option value="Recruitment">Recruitment</option>
                                    <option value="Payroll">Payroll</option>
                                </select>
                            </div>
                        </div>

                        {{-- Row 4: Position (Full Width) --}}
                        <div class="form-row full">
                            <div class="form-group-custom">
                                <label for="posisiSelect">Jabatan <span class="text-danger">*</span></label>
                                <select id="posisiSelect" class="form-control" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Senior Developer">Senior Developer</option>
                                    <option value="Developer">Developer</option>
                                    <option value="Specialist">Specialist</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-primary-custom" onclick="submitForm()">
                        <i class="fas fa-save me-2"></i>Simpan Karyawan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Reset Filter Function
        function resetFilters() {
            document.getElementById('filterDepartemen').value = '';
            document.getElementById('filterDivisi').value = '';
            document.getElementById('searchInput').value = '';
            console.log('Filter reset');
        }

        // Submit Form Function
        function submitForm() {
            const form = document.getElementById('addEmployeeForm');
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            
            // Here you would handle form submission
            alert('Form submitted! (This is a demo - no backend integration)');
            document.getElementById('addEmployeeModal').querySelector('[data-bs-dismiss="modal"]').click();
            form.reset();
        }

        // Action Button Event Listeners
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Edit functionality would go here');
            });
        });

        document.querySelectorAll('.btn-reset').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Reset password functionality would go here');
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus karyawan ini?')) {
                    alert('Delete functionality would go here');
                }
            });
        });

        // Search Function
        document.getElementById('searchInput').addEventListener('keyup', function() {
            console.log('Search:', this.value);
            // Filter logic would go here
        });

        // Filter Change Events
        document.getElementById('filterDepartemen').addEventListener('change', function() {
            console.log('Department filter:', this.value);
            // Filter logic would go here
        });

        document.getElementById('filterDivisi').addEventListener('change', function() {
            console.log('Division filter:', this.value);
            // Filter logic would go here
        });
    </script>
</body>
</html>
