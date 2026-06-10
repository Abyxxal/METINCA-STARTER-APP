{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Master Data')

{{-- Untuk menggunakan css --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable-jquery.css') }}">
    <style>
        /* Optimize table responsive behavior */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive table {
            width: 100%;
            min-width: 100%;
        }
        
        /* Make DataTable columns auto-fit content */
        table.dataTable thead th {
            padding: 10px 8px;
            font-size: 0.875rem;
        }
        
        table.dataTable tbody td {
            padding: 8px;
            font-size: 0.875rem;
        }
        
        /* Allow filter dropdowns to be more responsive */
        .form-select {
            min-width: 150px;
            max-width: 100%;
        }
        
        /* Tab content padding optimization */
        .tab-pane {
            padding: 0;
        }
        
        .tab-pane > div {
            width: 100%;
            overflow-x: auto;
        }

        /* Modal backdrop lebih gelap */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7);
            opacity: 1;
        }
    </style>
@endpush

{{-- Isi content --}}
@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    {{-- SECTION: Page Header --}}
                    {{-- Nama: Master Data --}}
                    {{-- Fungsi: Mengelola data referensi karyawan dan struktur organisasi (departemen) untuk mapping training requirements --}}
                    <h3>Master Data</h3>
                    <p class="text-subtitle text-muted">Kelola data karyawan dan departemen</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Master Data</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="card">
                <div class="card-body">
                    {{-- SECTION: Tab Navigation untuk Master Data --}}
                    {{-- Fungsi: Navigasi untuk mengelola 2 master data: Data Karyawan dan Departemen --}}
                    <!-- Nav tabs dengan styling distinction -->
                    <ul class="nav nav-tabs nav-justified border-bottom-2" id="masterDataTab" role="tablist">
                        {{-- TAB 1: Data Karyawan --}}
                        {{-- Isi: Daftar karyawan dengan NIK, nama, departemen, jabatan, shift, status (Active/Inactive) - support CRUD dan import Excel --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="karyawan-tab" data-bs-toggle="tab"
                                data-bs-target="#karyawan" type="button" role="tab" aria-controls="karyawan"
                                aria-selected="true">
                                <i class="bi bi-people-fill me-2" style="color: #6366f1;"></i><span style="font-weight: 600;">Data Karyawan</span>
                            </button>
                        </li>
                        {{-- TAB 2: Data Departemen --}}
                        {{-- Isi: Daftar departemen dengan jumlah divisi dan karyawan --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="departemen-tab" data-bs-toggle="tab"
                                data-bs-target="#departemen" type="button" role="tab" aria-controls="departemen"
                                aria-selected="false">
                                <i class="bi bi-building me-2" style="color: #8b5cf6;"></i><span style="font-weight: 600;">Departemen</span>
                            </button>
                        </li>
                    </ul>

                    <style>
                        .nav-link.active {
                            border-bottom: 3px solid #6366f1 !important;
                            color: #6366f1 !important;
                        }
                        .nav-link:hover {
                            border-radius: 4px 4px 0 0;
                        }
                    </style>

                    <!-- Tab panes -->
                    <div class="tab-content" id="masterDataTabContent">

                        {{-- ======================================================================== --}}
                        {{-- SECTION: TAB CONTENT 1 - DATA KARYAWAN --}}
                        {{-- Fungsi: Menampilkan dan mengelola profil semua karyawan dengan fitur CRUD --}}
                        {{-- Konten: Tabel karyawan, filter, pencarian, action buttons (Edit/Delete/Reset) --}}
                        {{-- ======================================================================== --}}
                        
                        <div class="tab-pane fade show active" id="karyawan" role="tabpanel"
                            aria-labelledby="karyawan-tab">
                            <div class="mt-4 px-3" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0) 100%); border-radius: 8px; padding: 20px;">
                                {{-- Header dengan tombol Tambah Karyawan --}}
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="mb-1"><i class="bi bi-people-fill me-2" style="color: #6366f1;"></i>Data Karyawan</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">Kelola informasi dan profil semua karyawan</p>
                                    </div>
                                    <button id="btnTambahKaryawan" type="button" class="btn btn-primary btn-sm btn-indigo" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Karyawan
                                    </button>
                                </div>

                                {{-- Filter Row: Filters + Reset + Search --}}
                                <div class="row g-2 mb-3 align-items-end">
                                    <div class="col-md-2">
                                        <label for="filterDepartemenKaryawan" class="form-label form-label-sm">Filter Departemen</label>
                                        <select id="filterDepartemenKaryawan" class="form-select form-select-sm">
                                            <option value="">Semua Dept</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="filterDivisiKaryawan" class="form-label form-label-sm">Filter Divisi</label>
                                        <select id="filterDivisiKaryawan" class="form-select form-select-sm">
                                            <option value="">Semua Divisi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="searchKaryawan" class="form-label form-label-sm">Cari Nama/NIK</label>
                                        <input type="text" class="form-control form-control-sm" id="searchKaryawan" placeholder="Ketik nama atau NIK...">
                                    </div>
                                    <div class="col-md-2 d-flex gap-1">
                                        <button id="btnResetFilter" class="btn btn-outline-secondary btn-sm" title="Reset semua filter">
                                            <i class="bi bi-arrow-counterclockwise"></i>Reset
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabel Data Karyawan - REBUILT FROM SCRATCH --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover" id="tableKaryawan">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 50px; text-align: center;">No</th>
                                                <th style="width: 100px; text-align: center;">NIK</th>
                                                <th>Nama</th>
                                                <th style="width: 200px;">Dept / Divisi</th>
                                                <th style="width: 100px; text-align: center;">Status</th>
                                                <th style="width: 120px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableKaryawanBody">
                                            <tr>
                                                <td colspan="6" class="text-center py-3">
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                                    <span class="ms-2">Memuat data...</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{-- INLINE SCRIPT: Auto-load table on page load --}}
                                <script>
                                // Define global functions FIRST before any table rendering
                                window.editKaryawan = function(nik) {
                                    console.log('🔧 editKaryawan called for:', nik);
                                    
                                    // Fetch employee data from API
                                    $.ajax({
                                        url: '/api/employees/' + nik,
                                        type: 'GET',
                                        success: function(response) {
                                            var emp = response.data;
                                            console.log('📥 Employee data fetched:', emp);
                                            
                                            // Populate edit modal
                                            $('#editKaryawanId').val(emp.nik);
                                            $('#editIdKaryawan').val(emp.nik);
                                            $('#editNamaKaryawan').val(emp.name || emp.nama_karyawan);
                                            $('#editEmailKaryawan').val(emp.email);
                                            $('#editPasswordKaryawan').val('');
                                            
                                            // Set department first - ini akan trigger cascade
                                            var deptSelect = document.getElementById('editDepartemenKaryawan');
                                            deptSelect.value = emp.department_id;
                                            
                                            // Trigger change event untuk rebuild division dropdown
                                            var deptChangeEvent = new Event('change', { bubbles: true });
                                            deptSelect.dispatchEvent(deptChangeEvent);
                                            
                                            // Set division setelah dropdown ter-rebuild (delay 100ms)
                                            setTimeout(function() {
                                                var divSelect = document.getElementById('editDivisiKaryawan');
                                                divSelect.value = emp.division_id;
                                                
                                                // Trigger change untuk rebuild position
                                                var divChangeEvent = new Event('change', { bubbles: true });
                                                divSelect.dispatchEvent(divChangeEvent);
                                                
                                                // Set position setelah dropdown ter-rebuild (delay 100ms lagi)
                                                setTimeout(function() {
                                                    var posSelect = document.getElementById('editJabatanKaryawan');
                                                    posSelect.value = emp.position_id;
                                                }, 100);
                                            }, 100);
                                            
                                            // Set status
                                            if (emp.status === 'Aktif') {
                                                $('#editStatusAktif').prop('checked', true);
                                            } else {
                                                $('#editStatusNonAktif').prop('checked', true);
                                            }
                                            
                                            // Show modal
                                            $('#modalEditKaryawan').modal('show');
                                        },
                                        error: function(xhr) {
                                            console.error('❌ Error:', xhr);
                                            Swal.fire({
                                                title: 'Gagal!',
                                                text: 'Gagal memuat data karyawan',
                                                icon: 'error'
                                            });
                                        }
                                    });
                                };
                                
                                window.hapusKaryawan = function(nik, nama) {
                                    console.log('🗑️ hapusKaryawan called for:', nik, nama);
                                    
                                    Swal.fire({
                                        title: 'Hapus Karyawan?',
                                        html: '<strong>' + nama + '</strong><br><small class="text-muted">NIK: ' + nik + '</small>',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc3545',
                                        cancelButtonColor: '#6c757d',
                                        confirmButtonText: 'Ya, Hapus',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            Swal.fire({
                                                title: 'Menghapus...',
                                                text: 'Tunggu sebentar...',
                                                allowOutsideClick: false,
                                                didOpen: () => Swal.showLoading()
                                            });
                                            
                                            $.ajax({
                                                url: '/api/employees/' + nik,
                                                type: 'DELETE',
                                                headers: {
                                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                                },
                                                success: function(response) {
                                                    console.log('✅ Delete success:', response);
                                                    Swal.fire({
                                                        title: 'Berhasil!',
                                                        text: 'Karyawan berhasil dihapus',
                                                        icon: 'success',
                                                        timer: 1200,
                                                        showConfirmButton: false
                                                    });
                                                    setTimeout(function() {
                                                        location.reload(true);
                                                    }, 1300);
                                                },
                                                error: function(xhr) {
                                                    console.error('❌ Delete error:', xhr);
                                                    Swal.fire({
                                                        title: 'Gagal!',
                                                        text: xhr.responseJSON?.message || 'Gagal menghapus karyawan',
                                                        icon: 'error'
                                                    });
                                                }
                                            });
                                        }
                                    });
                                };
                                
                                (function() {
                                    // Jalankan setelah DOM ready
                                    document.addEventListener('DOMContentLoaded', function() {
                                        console.log('🚀 AUTO-LOAD: Starting...');
                                        
                                        // Tunggu 500ms untuk pastikan semua script lain sudah load
                                        setTimeout(function() {
                                            loadKaryawanTableNow();
                                        }, 500);
                                    });
                                    
                                    // Fungsi load table yang standalone
                                    function loadKaryawanTableNow() {
                                        console.log('📡 Fetching employees...');
                                        var tbody = document.getElementById('tableKaryawanBody');
                                        
                                        if (!tbody) {
                                            console.error('❌ tbody not found!');
                                            return;
                                        }
                                        
                                        // Show loading
                                        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>';
                                        
                                        // Fetch data
                                        fetch('/api/employees', {
                                            method: 'GET',
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                            }
                                        })
                                        .then(function(response) {
                                            console.log('📨 Response status:', response.status);
                                            return response.json();
                                        })
                                        .then(function(result) {
                                            console.log('📦 Result:', result);
                                            
                                            if (result.success && result.data && result.data.length > 0) {
                                                console.log('✅ Found', result.data.length, 'employees');
                                                
                                                var html = '';
                                                result.data.forEach(function(emp, index) {
                                                    var statusBadge = emp.status === 'Aktif' 
                                                        ? '<span class="badge bg-success">Aktif</span>'
                                                        : '<span class="badge bg-secondary">Non-Aktif</span>';
                                                    
                                                    var deptDiv = '-';
                                                    if (emp.department || emp.division) {
                                                        deptDiv = (emp.department ? emp.department.name : '-') + ' / ' + (emp.division ? emp.division.name : '-');
                                                    }
                                                    
                                                    html += '<tr>' +
                                                        '<td class="text-center">' + (index + 1) + '</td>' +
                                                        '<td class="text-center"><strong>' + emp.nik + '</strong></td>' +
                                                        '<td>' + (emp.name || '-') + '</td>' +
                                                        '<td><small>' + deptDiv + '</small></td>' +
                                                        '<td class="text-center">' + statusBadge + '</td>' +
                                                        '<td class="text-center">' +
                                                            '<button class="btn btn-sm btn-warning me-1" onclick="editKaryawan(\'' + emp.nik + '\')" title="Edit"><i class="bi bi-pencil"></i></button>' +
                                                            '<button class="btn btn-sm btn-danger" onclick="hapusKaryawan(\'' + emp.nik + '\', \'' + (emp.name || '') + '\')" title="Hapus"><i class="bi bi-trash"></i></button>' +
                                                        '</td>' +
                                                    '</tr>';
                                                });
                                                
                                                tbody.innerHTML = html;
                                                console.log('✅ Table rendered!');
                                            } else {
                                                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada data karyawan</td></tr>';
                                                console.log('📋 No data to display');
                                            }
                                        })
                                        .catch(function(error) {
                                            console.error('❌ Fetch error:', error);
                                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3">Error: ' + error.message + '</td></tr>';
                                        });
                                    }
                                    
                                    // Expose function globally untuk reload
                                    window.reloadKaryawanTable = loadKaryawanTableNow;
                                })();
                                </script>
                            </div>
                        </div>

                        {{-- ======================================================================== --}}
                        {{-- SECTION: TAB CONTENT 2 - DEPARTEMEN --}}
                        {{-- Fungsi: Menampilkan dan mengelola daftar departemen dengan divisi dan karyawan --}}
                        {{-- Konten: Tabel departemen, action buttons (Edit/Delete) --}}
                        {{-- ======================================================================== --}}

                        <div class="tab-pane fade" id="departemen" role="tabpanel" aria-labelledby="departemen-tab">
                            <div class="mt-4 px-3" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(139, 92, 246, 0) 100%); border-radius: 8px; padding: 20px;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="mb-1"><i class="bi bi-building me-2" style="color: #8b5cf6;"></i>Daftar Departemen</h5>
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">Kelola struktur organisasi dan departemen</p>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm btn-violet" onclick="openTambahDepartemenModal()">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Departemen
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table id="tableDepartemen" class="table table-sm table-striped table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Nama Departemen</th>
                                                <th style="width: 120px; text-align: center;">Jumlah Divisi</th>
                                                <th style="width: 120px; text-align: center;">Jumlah Karyawan</th>
                                                <th style="width: 150px; text-align: center;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($departments as $index => $dept)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $dept->name }}</strong></td>
                                                <td class="text-center"><span class="badge bg-info">{{ $dept->divisions_count }}</span></td>
                                                <td class="text-center"><span class="badge bg-success">{{ $dept->employees_count }}</span></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-warning me-1" onclick="editDepartemen({{ $dept->id }}, '{{ $dept->name }}')" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="hapusDepartemen({{ $dept->id }}, '{{ $dept->name }}')" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Tidak ada data departemen</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Departemen Tab -->
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================== --}}
        {{-- SECTION: MODAL - DEPARTEMEN --}}
        {{-- Modal-modal untuk fitur CRUD Departemen (Tambah, Edit, Delete) --}}
        {{-- ======================================================================== --}}

        {{-- ======================================================================== --}}
        {{-- SECTION: MODAL - DEPARTEMEN --}}
        {{-- Modal-modal untuk fitur CRUD Departemen (Tambah, Edit, Delete) --}}
        {{-- ======================================================================== --}}

        {{-- MODAL 1: TAMBAH DEPARTEMEN (dengan nested divisions & positions) --}}
        <div class="modal fade" id="modalTambahDept" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Departemen</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formTambahDept" onsubmit="return false;">
                            {{-- Nama Departemen --}}
                            <div class="mb-4">
                                <label for="namaDeptTambah" class="form-label fw-bold">Nama Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namaDeptTambah" placeholder="Masukkan nama departemen" required>
                            </div>

                            <hr>

                            {{-- Divisi Section dengan Nested Positions --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-diagram-3 me-1"></i>Divisi & Jabatan <span class="text-danger">*</span>
                                </label>
                                
                                <div id="containerDivisiTambah" class="mb-3">
                                    <!-- Divisi dengan nested positions akan ditambah di sini -->
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.tambahFieldDivisiTambah()">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Divisi
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="window.simpanDepartemen()">
                            <i class="bi bi-check-circle me-1"></i>Simpan Departemen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL 2: EDIT DEPARTEMEN --}}
        <div class="modal fade" id="modalEditDept" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formEditDept">
                            <input type="hidden" id="editIdDept">
                            
                            {{-- Nama Departemen --}}
                            <div class="mb-4">
                                <label for="editNamaDept" class="form-label fw-bold">Nama Departemen <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editNamaDept" placeholder="Masukkan nama departemen" required>
                            </div>

                            <hr>

                            {{-- Divisi & Jabatan Section dengan Nested Positions --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-diagram-3 me-1"></i>Divisi & Jabatan <span class="text-danger">*</span>
                                </label>
                                
                                <div id="containerDivisiEdit" class="mb-3">
                                    <!-- Divisi dengan nested positions akan ditampilkan di sini -->
                                </div>
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.tambahFieldDivisiEdit()">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Divisi
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-warning" onclick="window.updateDepartemen()">
                            <i class="bi bi-check-circle me-1"></i>Update Departemen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL 3: KONFIRMASI HAPUS DEPARTEMEN --}}
        <div class="modal fade" id="modalHapusDept" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin hapus departemen: <strong id="namaHapusDept"></strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" onclick="konfirmasiHapusDepartemen()">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================================== --}}
        {{-- SECTION: MODAL - DATA KARYAWAN --}}
        {{-- Modal-modal untuk fitur CRUD Data Karyawan (Tambah, Edit, Delete) --}}
        {{-- ======================================================================== --}}

        {{-- ============================================ --}}
        {{-- MODAL TAMBAH KARYAWAN - REBUILT FROM SCRATCH --}}
        {{-- ============================================ --}}
        <div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-labelledby="modalTambahKaryawanLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTambahKaryawanLabel">
                            <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahKaryawan">
                            <div class="row">
                                {{-- LEFT COLUMN --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nikTambah" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nikTambah" placeholder="Contoh: E001" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="namaTambah" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="namaTambah" placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="emailTambah" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="emailTambah" placeholder="nama@company.com" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="passwordTambah" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="passwordTambah" placeholder="Minimal 6 karakter" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="joinDateTambah" class="form-label fw-bold">Join Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="joinDateTambah" required>
                                    </div>
                                </div>

                                {{-- RIGHT COLUMN --}}
                                <div class="col-md-6">
                                    {{-- DEPARTMENT - Render langsung dari server --}}
                                    <div class="mb-3">
                                        <label for="departmentTambah" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                        <select class="form-select" id="departmentTambah" name="department_id" required>
                                            <option value="">-- Pilih Departemen --</option>
                                            @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    {{-- DIVISION - Render SEMUA dari server, filter via JS --}}
                                    <div class="mb-3">
                                        <label for="divisionTambah" class="form-label fw-bold">Division <span class="text-danger">*</span></label>
                                        <select class="form-select" id="divisionTambah" name="division_id" required disabled>
                                            <option value="">-- Pilih Departemen Dulu --</option>
                                            @foreach($divisions as $div)
                                            <option value="{{ $div->id }}" data-dept="{{ $div->department_id }}">{{ $div->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Pilih departemen terlebih dahulu</small>
                                    </div>
                                    
                                    {{-- POSITION - Render SEMUA dari server, filter via JS --}}
                                    <div class="mb-3">
                                        <label for="positionTambah" class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                                        <select class="form-select" id="positionTambah" name="position_id" required disabled>
                                            <option value="">-- Pilih Divisi Dulu --</option>
                                            @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}" data-div="{{ $pos->division_id }}">{{ $pos->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Pilih divisi terlebih dahulu</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="statusTambah" id="statusAktifTambah" value="Aktif" checked>
                                                <label class="form-check-label" for="statusAktifTambah">Aktif</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="statusTambah" id="statusNonAktifTambah" value="Non-Aktif">
                                                <label class="form-check-label" for="statusNonAktifTambah">Non-Aktif</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSimpanKaryawan">
                            <i class="bi bi-check-circle me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- INLINE SCRIPT untuk cascade - langsung di bawah modal --}}
        <script>
        (function() {
            // Simpan semua options saat page load
            var divisionOptions = [];
            var positionOptions = [];
            
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 CASCADE INIT START');
                
                // Cache semua division options
                var divSelect = document.getElementById('divisionTambah');
                if (divSelect) {
                    Array.from(divSelect.options).forEach(function(opt) {
                        if (opt.value) {
                            divisionOptions.push({
                                value: opt.value,
                                text: opt.text,
                                deptId: opt.getAttribute('data-dept')
                            });
                        }
                    });
                    console.log('📦 Cached ' + divisionOptions.length + ' divisions');
                }
                
                // Cache semua position options
                var posSelect = document.getElementById('positionTambah');
                if (posSelect) {
                    Array.from(posSelect.options).forEach(function(opt) {
                        if (opt.value) {
                            positionOptions.push({
                                value: opt.value,
                                text: opt.text,
                                divId: opt.getAttribute('data-div')
                            });
                        }
                    });
                    console.log('📦 Cached ' + positionOptions.length + ' positions');
                }
                
                // Department change handler
                var deptSelect = document.getElementById('departmentTambah');
                if (deptSelect) {
                    deptSelect.addEventListener('change', function() {
                        var deptId = this.value;
                        console.log('🔄 Department changed: ' + deptId);
                        
                        // Reset & rebuild division dropdown
                        divSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';
                        divSelect.disabled = !deptId;
                        
                        if (deptId) {
                            var count = 0;
                            divisionOptions.forEach(function(opt) {
                                if (opt.deptId === deptId) {
                                    var option = document.createElement('option');
                                    option.value = opt.value;
                                    option.text = opt.text;
                                    option.setAttribute('data-dept', opt.deptId);
                                    divSelect.appendChild(option);
                                    count++;
                                }
                            });
                            console.log('   ✓ Added ' + count + ' divisions');
                        }
                        
                        // Reset position
                        posSelect.innerHTML = '<option value="">-- Pilih Divisi Dulu --</option>';
                        posSelect.disabled = true;
                    });
                }
                
                // Division change handler
                if (divSelect) {
                    divSelect.addEventListener('change', function() {
                        var divId = this.value;
                        console.log('🔄 Division changed: ' + divId);
                        
                        // Reset & rebuild position dropdown
                        posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                        posSelect.disabled = !divId;
                        
                        if (divId) {
                            var count = 0;
                            positionOptions.forEach(function(opt) {
                                if (opt.divId === divId) {
                                    var option = document.createElement('option');
                                    option.value = opt.value;
                                    option.text = opt.text;
                                    option.setAttribute('data-div', opt.divId);
                                    posSelect.appendChild(option);
                                    count++;
                                }
                            });
                            console.log('   ✓ Added ' + count + ' positions');
                        }
                    });
                }
                
                console.log('✅ CASCADE INIT COMPLETE');
                
                // ============================================
                // HANDLER SIMPAN KARYAWAN
                // ============================================
                console.log('🔧 Attaching SAVE button handler...');
                
                var btnSimpan = document.getElementById('btnSimpanKaryawan');
                if (!btnSimpan) {
                    console.error('❌ Button #btnSimpanKaryawan NOT FOUND!');
                    return;
                }
                
                console.log('✅ Button found, attaching click handler...');
                
                btnSimpan.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('🔘 SIMPAN CLICKED!');
                    
                    // Collect data
                    var data = {
                        nik: document.getElementById('nikTambah').value.trim(),
                        name: document.getElementById('namaTambah').value.trim(),
                        email: document.getElementById('emailTambah').value.trim(),
                        password: document.getElementById('passwordTambah').value,
                        department_id: document.getElementById('departmentTambah').value,
                        division_id: document.getElementById('divisionTambah').value,
                        position_id: document.getElementById('positionTambah').value,
                        join_date: document.getElementById('joinDateTambah').value,
                        status: document.querySelector('input[name="statusTambah"]:checked')?.value || 'Aktif'
                    };
                    
                    console.log('📋 Data:', data);
                    
                    // Validate
                    var missing = [];
                    if (!data.nik) missing.push('NIK');
                    if (!data.name) missing.push('Nama');
                    if (!data.email) missing.push('Email');
                    if (!data.password) missing.push('Password');
                    if (!data.department_id) missing.push('Department');
                    if (!data.division_id) missing.push('Division');
                    if (!data.position_id) missing.push('Position');
                    if (!data.join_date) missing.push('Join Date');
                    
                    if (missing.length > 0) {
                        console.warn('⚠️ Missing:', missing);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Tidak Lengkap',
                            html: 'Field yang belum diisi:<br><strong>' + missing.join(', ') + '</strong>'
                        });
                        return;
                    }
                    
                    console.log('✅ Validation OK, sending to API...');
                    
                    // Disable button
                    btnSimpan.disabled = true;
                    btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                    
                    // Send AJAX
                    fetch('/api/employees', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => {
                        console.log('📨 Response status:', response.status);
                        return response.json().then(json => ({status: response.status, body: json}));
                    })
                    .then(result => {
                        console.log('📦 Result:', result);
                        
                        if (result.status >= 200 && result.status < 300) {
                            console.log('✅ SUCCESS!');
                            
                            // Close modal PROPERLY
                            var modalEl = document.getElementById('modalTambahKaryawan');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                modal.hide();
                            }
                            
                            // Force remove backdrop and body classes
                            setTimeout(function() {
                                document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                                    el.remove();
                                });
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';
                            }, 200);
                            
                            // Show success & auto reload
                            console.log('✅ Menampilkan SweetAlert...');
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Karyawan "' + data.name + '" berhasil ditambahkan',
                                timer: 1200,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            });
                            
                            // Force reload setelah 1.3 detik (sedikit setelah SweetAlert tutup)
                            setTimeout(function() {
                                console.log('🔄 Forcing reload now...');
                                location.reload(true);
                            }, 1300);
                        } else {
                            console.error('❌ Error:', result.body);
                            var errorMsg = result.body.message || 'Gagal menyimpan data';
                            if (result.body.errors) {
                                errorMsg = Object.values(result.body.errors).flat().join('\\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMsg
                            });
                        }
                    })
                    .catch(error => {
                        console.error('❌ FETCH ERROR:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan: ' + error.message
                        });
                    })
                    .finally(() => {
                        btnSimpan.disabled = false;
                        btnSimpan.innerHTML = '<i class="bi bi-check-circle me-1"></i>Simpan';
                    });
                });
                
                console.log('✅ SAVE handler attached!');
            });
        })();
        </script>

        {{-- Modal Edit Karyawan --}}
        <div class="modal fade" id="modalEditKaryawan" tabindex="-1" aria-labelledby="modalEditKaryawanLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalEditKaryawanLabel">
                            <i class="bi bi-pencil-square me-2"></i>Edit Data Karyawan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditKaryawan">
                            {{-- Hidden field untuk store NIK lama --}}
                            <input type="hidden" id="editKaryawanId" value="">
                            
                            {{-- Two Column Layout --}}
                            <div class="row">
                                {{-- LEFT COLUMN: Account & Personal Info --}}
                                <div class="col-md-6">
                                    {{-- NIK --}}
                                    <div class="mb-3">
                                        <label for="editIdKaryawan" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editIdKaryawan" placeholder="Contoh: E001" required>
                                    </div>

                                    {{-- Full Name --}}
                                    <div class="mb-3">
                                        <label for="editNamaKaryawan" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editNamaKaryawan" placeholder="Masukkan nama lengkap" required>
                                    </div>

                                    {{-- Email Address --}}
                                    <div class="mb-3">
                                        <label for="editEmailKaryawan" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="editEmailKaryawan" placeholder="nama@company.com" required>
                                    </div>

                                    {{-- Password --}}
                                    <div class="mb-3">
                                        <label for="editPasswordKaryawan" class="form-label fw-bold">Password <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                                        <input type="password" class="form-control" id="editPasswordKaryawan" placeholder="Masukkan password baru (opsional)">
                                    </div>
                                </div>

                                {{-- RIGHT COLUMN: Employment Data & Photo --}}
                                <div class="col-md-6">
                                    {{-- Department --}}
                                    <div class="mb-3">
                                        <label for="editDepartemenKaryawan" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editDepartemenKaryawan" required>
                                            <option value="">-- Pilih Departemen --</option>
                                            @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Division --}}
                                    <div class="mb-3">
                                        <label for="editDivisiKaryawan" class="form-label fw-bold">Division <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editDivisiKaryawan" required>
                                            <option value="">-- Pilih Divisi --</option>
                                            @foreach($divisions as $div)
                                            <option value="{{ $div->id }}" data-dept="{{ $div->department_id }}">{{ $div->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Position --}}
                                    <div class="mb-3">
                                        <label for="editJabatanKaryawan" class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editJabatanKaryawan" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                            @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}" data-div="{{ $pos->division_id }}">{{ $pos->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Status --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusAktif" value="Aktif" checked>
                                                <label class="form-check-label" for="editStatusAktif">
                                                    Aktif
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="editStatusKaryawan" id="editStatusNonAktif" value="Non-Aktif">
                                                <label class="form-check-label" for="editStatusNonAktif">
                                                    Non-Aktif
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            {{-- End of Two Column Layout --}}
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </button>
                        <button type="button" class="btn btn-info text-white" id="btnUpdateKaryawan">
                            <i class="bi bi-check-circle me-2"></i>Perbarui Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- INLINE SCRIPT untuk cascade EDIT MODAL - sama seperti tambah --}}
        <script>
        (function() {
            // Cache untuk EDIT modal
            var editDivisionOptions = [];
            var editPositionOptions = [];
            
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 CASCADE EDIT MODAL INIT');
                
                // Cache division options untuk edit
                var editDivSelect = document.getElementById('editDivisiKaryawan');
                if (editDivSelect) {
                    Array.from(editDivSelect.options).forEach(function(opt) {
                        if (opt.value) {
                            editDivisionOptions.push({
                                value: opt.value,
                                text: opt.text,
                                deptId: opt.getAttribute('data-dept')
                            });
                        }
                    });
                    console.log('📦 Cached ' + editDivisionOptions.length + ' divisions for edit');
                }
                
                // Cache position options untuk edit
                var editPosSelect = document.getElementById('editJabatanKaryawan');
                if (editPosSelect) {
                    Array.from(editPosSelect.options).forEach(function(opt) {
                        if (opt.value) {
                            editPositionOptions.push({
                                value: opt.value,
                                text: opt.text,
                                divId: opt.getAttribute('data-div')
                            });
                        }
                    });
                    console.log('📦 Cached ' + editPositionOptions.length + ' positions for edit');
                }
                
                // Department change handler untuk EDIT
                var editDeptSelect = document.getElementById('editDepartemenKaryawan');
                if (editDeptSelect) {
                    editDeptSelect.addEventListener('change', function() {
                        var deptId = this.value;
                        console.log('🔄 EDIT Department changed: ' + deptId);
                        
                        // Reset & rebuild division dropdown
                        editDivSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';
                        editDivSelect.disabled = !deptId;
                        
                        if (deptId) {
                            var count = 0;
                            editDivisionOptions.forEach(function(opt) {
                                if (opt.deptId === deptId) {
                                    var option = document.createElement('option');
                                    option.value = opt.value;
                                    option.text = opt.text;
                                    option.setAttribute('data-dept', opt.deptId);
                                    editDivSelect.appendChild(option);
                                    count++;
                                }
                            });
                            console.log('   ✓ Added ' + count + ' divisions to edit');
                        }
                        
                        // Reset position
                        editPosSelect.innerHTML = '<option value="">-- Pilih Divisi Dulu --</option>';
                        editPosSelect.disabled = true;
                    });
                }
                
                // Division change handler untuk EDIT
                if (editDivSelect) {
                    editDivSelect.addEventListener('change', function() {
                        var divId = this.value;
                        console.log('🔄 EDIT Division changed: ' + divId);
                        
                        // Reset & rebuild position dropdown
                        editPosSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                        editPosSelect.disabled = !divId;
                        
                        if (divId) {
                            var count = 0;
                            editPositionOptions.forEach(function(opt) {
                                if (opt.divId === divId) {
                                    var option = document.createElement('option');
                                    option.value = opt.value;
                                    option.text = opt.text;
                                    option.setAttribute('data-div', opt.divId);
                                    editPosSelect.appendChild(option);
                                    count++;
                                }
                            });
                            console.log('   ✓ Added ' + count + ' positions to edit');
                        }
                    });
                }
                
                console.log('✅ CASCADE EDIT MODAL COMPLETE');
                
                // ============================================
                // HANDLER UPDATE KARYAWAN
                // ============================================
                console.log('🔧 Attaching UPDATE button handler...');
                
                var btnUpdate = document.getElementById('btnUpdateKaryawan');
                if (!btnUpdate) {
                    console.error('❌ Button #btnUpdateKaryawan NOT FOUND!');
                } else {
                    console.log('✅ Update button found, attaching handler...');
                    
                    btnUpdate.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('🔘 UPDATE CLICKED!');
                        
                        var id = document.getElementById('editKaryawanId').value;
                        var nik = document.getElementById('editIdKaryawan').value;
                        var nama = document.getElementById('editNamaKaryawan').value;
                        var email = document.getElementById('editEmailKaryawan').value;
                        var password = document.getElementById('editPasswordKaryawan').value;
                        var departemenId = document.getElementById('editDepartemenKaryawan').value;
                        var divisiId = document.getElementById('editDivisiKaryawan').value;
                        var jabatanId = document.getElementById('editJabatanKaryawan').value;
                        var status = document.querySelector('input[name="editStatusKaryawan"]:checked')?.value;
                        
                        console.log('📋 Update Data:', {
                            id, nik, nama, email, departemenId, divisiId, jabatanId, status
                        });
                        
                        // Validation
                        if (!id) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Data karyawan tidak dapat diidentifikasi'
                            });
                            return;
                        }
                        
                        if (!nik || !nama || !email || !departemenId || !divisiId || !jabatanId) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Data Tidak Lengkap',
                                text: 'Semua field harus diisi!'
                            });
                            return;
                        }
                        
                        // Disable button
                        btnUpdate.disabled = true;
                        btnUpdate.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memperbarui...';
                        
                        // Create FormData
                        var formData = new FormData();
                        formData.append('nik', nik);
                        formData.append('name', nama);
                        formData.append('email', email);
                        formData.append('department_id', departemenId);
                        formData.append('division_id', divisiId);
                        formData.append('position_id', jabatanId);
                        formData.append('status', status);
                        
                        if (password && password.trim().length > 0) {
                            formData.append('password', password);
                        }
                        
                        // Send via fetch with PUT method
                        fetch('/api/employees/' + id, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('📨 Response status:', response.status);
                            return response.json().then(json => ({status: response.status, body: json}));
                        })
                        .then(result => {
                            console.log('📦 Result:', result);
                            
                            if (result.status >= 200 && result.status < 300) {
                                console.log('✅ UPDATE SUCCESS!');
                                
                                // Close modal
                                var modalEl = document.getElementById('modalEditKaryawan');
                                var modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) {
                                    modal.hide();
                                }
                                
                                // Remove backdrop
                                setTimeout(function() {
                                    document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                                        el.remove();
                                    });
                                    document.body.classList.remove('modal-open');
                                    document.body.style.overflow = '';
                                }, 200);
                                
                                // Show success & reload
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data karyawan berhasil diperbarui',
                                    timer: 1200,
                                    showConfirmButton: false
                                });
                                
                                setTimeout(function() {
                                    location.reload(true);
                                }, 1300);
                            } else {
                                console.error('❌ Error:', result.body);
                                var errorMsg = result.body.message || 'Gagal memperbarui data';
                                if (result.body.errors) {
                                    errorMsg = Object.values(result.body.errors).flat().join('\\n');
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: errorMsg
                                });
                            }
                        })
                        .catch(error => {
                            console.error('❌ FETCH ERROR:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan: ' + error.message
                            });
                        })
                        .finally(() => {
                            btnUpdate.disabled = false;
                            btnUpdate.innerHTML = '<i class="bi bi-check-circle me-2"></i>Perbarui Data';
                        });
                    });
                }
            });
        })();
        </script>

        {{-- Modal Hapus Karyawan --}}
        <div class="modal fade" id="modalHapusKaryawan" tabindex="-1" aria-labelledby="modalHapusKaryawanLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalHapusKaryawanLabel">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Karyawan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="hapusKaryawanId">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Perhatian!</strong> Data karyawan yang dihapus tidak dapat dikembalikan.
                        </div>
                        <p class="mb-2">Anda yakin ingin menghapus karyawan berikut?</p>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-0" id="hapusNamaKaryawan"></h6>
                                        <small class="text-muted" id="hapusNikKaryawan"></small>
                                    </div>
                                </div>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="100"><strong>Departemen:</strong></td>
                                        <td id="hapusDepartemenKaryawan"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jabatan:</strong></td>
                                        <td id="hapusJabatanKaryawan"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td id="hapusStatusKaryawan"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <p class="text-danger mt-3 mb-0">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Data ini akan <strong>dihapus secara permanen</strong> dari sistem.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="btnKonfirmasiHapusKaryawan">
                            <i class="bi bi-trash me-1"></i>Ya, Hapus Karyawan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Untuk menggunakan js --}}
@push('scripts')
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ===== DEPARTEMEN FUNCTIONS (OUTSIDE DOCUMENT.READY) =====
        console.log('🔧 Loading department functions...');
        
        window.openTambahDepartemenModal = function() {
            console.log('🔧 openTambahDepartemenModal() called');
            
            const modalEl = document.getElementById('modalTambahDept');
            
            if (!modalEl) {
                console.error('Modal #modalTambahDept not found');
                return;
            }

            try {
                let bsModal = bootstrap.Modal.getInstance(modalEl);
                if (!bsModal) {
                    bsModal = new bootstrap.Modal(modalEl);
                }
                bsModal.show();
                console.log('✅ Modal opened');
            } catch (e) {
                console.error('Bootstrap error:', e);
                
                try {
                    $('#modalTambahDept').modal('show');
                    console.log('✅ Fallback to jQuery');
                } catch (e2) {
                    console.error('jQuery error:', e2);
                }
            }
        };
        
        // ===== FUNGSI TAMBAH FIELD UNTUK MODAL EDIT =====
        // Harus didefinisikan SEBELUM editDepartemen agar tersedia saat modal dibuka
        
        // Tambah division baru saat edit
        window.tambahFieldDivisiEdit = function() {
            console.log('➕ tambahFieldDivisiEdit() called');
            var container = document.getElementById('containerDivisiEdit');
            
            if (!container) {
                console.error('❌ containerDivisiEdit not found!');
                return;
            }
            
            var divIndex = container.querySelectorAll('.divisi-wrapper-edit').length;
            console.log('   Current division count:', divIndex);
            
            var html = `
                <div class="mb-3 p-3 border-2 border-primary rounded bg-light divisi-wrapper-edit" data-div-id="new_${divIndex}">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm divisi-name-edit" placeholder="Nama divisi baru" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.divisi-wrapper-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Nested Positions Container -->
                    <div class="ms-3 mb-2 p-3 bg-white rounded border">
                        <label class="form-label small fw-bold mb-2">
                            <i class="bi bi-briefcase me-1"></i>Jabatan untuk divisi ini:
                        </label>
                        <div class="positions-container-edit" data-div-id="new_${divIndex}">
                            <!-- Positions akan ditambah di sini -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="window.tambahFieldPositionEdit('new_${divIndex}')">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            console.log('✅ Division field added successfully');
        };

        // Tambah position untuk division tertentu saat edit
        window.tambahFieldPositionEdit = function(divId) {
            console.log('➕ tambahFieldPositionEdit() called for divId:', divId);
            var container = document.querySelector(`.positions-container-edit[data-div-id="${divId}"]`);
            
            if (!container) {
                console.error('❌ Position container not found for divId:', divId);
                return;
            }
            
            var html = `
                <div class="mb-2 p-2 bg-light border rounded position-field-edit" data-pos-id="new_pos">
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm position-name-edit" placeholder="Nama jabatan" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.position-field-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            console.log('✅ Position field added successfully');
        };
        
        // ===== FUNGSI EDIT DEPARTEMEN =====
        
        window.editDepartemen = function(id, nama) {
            console.log('📝 Edit department:', id, nama);
            
            // Show loading
            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Fetch department detail with divisions and positions
            fetch(`/api/departments/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                
                if (data.success && data.data) {
                    const dept = data.data;
                    
                    // Set department ID and name
                    document.getElementById('editIdDept').value = dept.id;
                    document.getElementById('editNamaDept').value = dept.name;
                    
                    // Clear existing divisions
                    const container = document.getElementById('containerDivisiEdit');
                    container.innerHTML = '';
                    
                    // Populate divisions and positions
                    if (dept.divisions && dept.divisions.length > 0) {
                        dept.divisions.forEach((division, divIndex) => {
                            // Add division wrapper
                            const divHtml = `
                                <div class="mb-3 p-3 border-2 border-primary rounded bg-light divisi-wrapper-edit" data-div-id="${division.id}">
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-10">
                                            <input type="text" class="form-control form-control-sm divisi-name-edit" placeholder="Nama divisi" value="${division.name}" required>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.divisi-wrapper-edit').remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Nested Positions Container -->
                                    <div class="ms-3 mb-2 p-3 bg-white rounded border">
                                        <label class="form-label small fw-bold mb-2">
                                            <i class="bi bi-briefcase me-1"></i>Jabatan untuk divisi ini:
                                        </label>
                                        <div class="positions-container-edit" data-div-id="${division.id}">
                                            <!-- Positions will be added here -->
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="window.tambahFieldPositionEdit('${division.id}')">
                                            <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                                        </button>
                                    </div>
                                </div>
                            `;
                            container.insertAdjacentHTML('beforeend', divHtml);
                            
                            // Add positions for this division
                            if (division.positions && division.positions.length > 0) {
                                const posContainer = container.querySelector(`.positions-container-edit[data-div-id="${division.id}"]`);
                                division.positions.forEach(position => {
                                    const posHtml = `
                                        <div class="mb-2 p-2 bg-light border rounded position-field-edit" data-pos-id="${position.id}">
                                            <div class="row align-items-center">
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control form-control-sm position-name-edit" placeholder="Nama jabatan" value="${position.name}" required>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.position-field-edit').remove()">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    posContainer.insertAdjacentHTML('beforeend', posHtml);
                                });
                            }
                        });
                    }
                    
                    // Show modal
                    const modal = document.getElementById('modalEditDept');
                    let bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                    bsModal.show();
                    
                    console.log('✅ Modal populated with', dept.divisions?.length || 0, 'divisions');
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal memuat data departemen',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error loading department:', error);
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat data departemen: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        };
        
        window.hapusDepartemen = function(id, nama) {
            const modal = document.getElementById('modalHapusDept');
            if (modal) {
                document.getElementById('hapusIdDept').value = id;
                document.getElementById('hapusNamaDept').textContent = nama;
                let bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                bsModal.show();
            }
        };
        
        window.konfirmasiHapusDepartemen = function() {
            const deptId = document.getElementById('hapusIdDept').value;
            if (!deptId) return;
            
            fetch(`/api/departments/${deptId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalHapusDept'));
                    modal.hide();
                    
                    // Reload table after modal close
                    setTimeout(() => {
                        if (typeof window.loadBothTables === 'function') {
                            window.loadBothTables();
                        }
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Departemen berhasil dihapus',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
                    }, 300);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal menghapus departemen',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        };

        window.simpanDepartemen = function() {
            const nama = document.getElementById('namaDeptTambah').value.trim();
            if (!nama) {
                alert('Nama departemen harus diisi!');
                return;
            }
            
            // Collect divisions and their positions
            const divisions = [];
            const divisiWrappers = document.querySelectorAll('#containerDivisiTambah .divisi-wrapper');
            
            if (divisiWrappers.length === 0) {
                alert('Minimal harus ada 1 divisi!');
                return;
            }
            
            divisiWrappers.forEach((wrapper, index) => {
                const divisiName = wrapper.querySelector('.divisi-name-input').value.trim();
                
                if (!divisiName) {
                    alert(`Nama divisi ${index + 1} harus diisi!`);
                    return;
                }
                
                // Collect positions for this division
                const positions = [];
                const positionInputs = wrapper.querySelectorAll('.position-name-input');
                
                positionInputs.forEach(input => {
                    const posName = input.value.trim();
                    if (posName) {
                        positions.push({ name: posName });
                    }
                });
                
                if (positions.length === 0) {
                    alert(`Divisi "${divisiName}" harus memiliki minimal 1 jabatan!`);
                    return;
                }
                
                divisions.push({
                    name: divisiName,
                    positions: positions
                });
            });
            
            const payload = {
                name: nama,
                divisions: divisions
            };
            
            console.log('📤 Sending payload:', payload);
            
            fetch('/api/departments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            }).then(response => response.json())
            .then(data => {
                console.log('📥 Response:', data);
                
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahDept'));
                    
                    // Close modal and reset form first
                    modal.hide();
                    document.getElementById('namaDeptTambah').value = '';
                    document.getElementById('containerDivisiTambah').innerHTML = '';
                    
                    // Wait for modal to close, then reload table and show success message
                    setTimeout(() => {
                        if (typeof window.loadBothTables === 'function') {
                            window.loadBothTables();
                        }
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Departemen dengan divisi dan jabatan berhasil disimpan',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
                    }, 300); // Wait for modal animation to complete
                } else {
                    alert('Gagal: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                alert('Terjadi error saat menyimpan. Cek console untuk detail.');
            });
        };

        window.updateDepartemen = function() {
            const id = document.getElementById('editIdDept').value;
            const nama = document.getElementById('editNamaDept').value.trim();
            
            if (!nama) {
                Swal.fire({title: 'Validasi!', text: 'Nama departemen harus diisi', icon: 'warning'});
                return;
            }
            
            // Collect divisions data
            const divWrappers = document.querySelectorAll('#containerDivisiEdit .divisi-wrapper-edit');
            const divisions = [];
            
            divWrappers.forEach(divWrapper => {
                const divId = divWrapper.getAttribute('data-div-id');
                const divName = divWrapper.querySelector('.divisi-name-edit').value.trim();
                
                if (!divName) {
                    Swal.fire({title: 'Validasi!', text: 'Semua nama divisi harus diisi', icon: 'warning'});
                    return;
                }
                
                const divisionData = {
                    name: divName,
                    positions: []
                };
                
                // Tambahkan ID hanya jika bukan 'new_'
                if (divId && !divId.startsWith('new_')) {
                    divisionData.id = parseInt(divId);
                }
                
                // Collect positions untuk division ini
                const posFields = divWrapper.querySelectorAll('.position-field-edit');
                posFields.forEach(posField => {
                    const posId = posField.getAttribute('data-pos-id');
                    const posName = posField.querySelector('.position-name-edit').value.trim();
                    
                    if (posName) {
                        const positionData = { name: posName };
                        if (posId && posId !== 'new_pos') {
                            positionData.id = parseInt(posId);
                        }
                        divisionData.positions.push(positionData);
                    }
                });
                
                divisions.push(divisionData);
            });
            
            const payload = {
                name: nama,
                divisions: divisions
            };
            
            console.log('📤 Update payload:', payload);
            
            fetch(`/api/departments/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            }).then(response => response.json())
            .then(data => {
                console.log('📥 Update response:', data);
                
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditDept'));
                    modal.hide();
                    
                    // Clear form
                    document.getElementById('editIdDept').value = '';
                    document.getElementById('editNamaDept').value = '';
                    document.getElementById('containerDivisiEdit').innerHTML = '';
                    
                    // Reload table after modal close
                    setTimeout(() => {
                        if (typeof window.loadBothTables === 'function') {
                            window.loadBothTables();
                        }
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Departemen dengan divisi dan jabatan berhasil diperbarui',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        });
                    }, 300);
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Gagal mengupdate departemen',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        };
        
        window.tambahFieldDivisiTambah = function() {
            try {
                var container = document.getElementById('containerDivisiTambah');
                
                if (!container) {
                    console.error('❌ containerDivisiTambah not found!');
                    return false;
                }
                
                var divIndex = container.querySelectorAll('.divisi-wrapper').length;
                console.log('📍 Adding divisi field with index:', divIndex);
                
                var html = `
                    <div class="mb-3 p-3 border-2 border-primary rounded bg-light divisi-wrapper" data-div-index="${divIndex}">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-1">
                                <span class="badge bg-primary">Divisi ${divIndex + 1}</span>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control form-control-sm divisi-name-input" placeholder="Nama divisi (contoh: IT Support)" required>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.divisi-wrapper').remove()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="ms-3 mb-2 p-3 bg-white rounded border">
                            <label class="form-label small fw-bold mb-2">
                                <i class="bi bi-briefcase me-1"></i>Jabatan untuk divisi ini:
                            </label>
                            <div class="positions-container" data-div-index="${divIndex}">
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="window.tambahFieldPositionTambah(${divIndex})">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                            </button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                console.log('✅ Divisi field added successfully');
                return true;
            } catch(error) {
                console.error('❌ Error in tambahFieldDivisiTambah:', error.message);
                console.error('Stack:', error.stack);
                return false;
            }
        };
        
        window.tambahFieldPositionTambah = function(divIndex) {
            try {
                var container = document.querySelector('.positions-container[data-div-index="' + divIndex + '"]');
                
                if (!container) {
                    console.error('❌ positions-container with data-div-index="' + divIndex + '" not found');
                    return false;
                }
                
                var posIndex = container.querySelectorAll('.position-item').length;
                console.log('📍 Adding position field with div-index:', divIndex, 'pos-index:', posIndex);
                
                var html = `
                    <div class="position-item input-group input-group-sm mb-2">
                        <input type="text" class="form-control position-name-input" placeholder="Nama jabatan (contoh: Manager)" required>
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.position-item').remove()">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                console.log('✅ Position field added successfully');
                return true;
            } catch(error) {
                console.error('❌ Error in tambahFieldPositionTambah:', error.message);
                console.error('Stack:', error.stack);
                return false;
            }
        };
        
        console.log('✅ Department functions loaded!');
        console.log('   - openTambahDepartemenModal:', typeof window.openTambahDepartemenModal);
        console.log('   - tambahFieldDivisiTambah:', typeof window.tambahFieldDivisiTambah);
        console.log('   - tambahFieldPositionTambah:', typeof window.tambahFieldPositionTambah);
    </script>
    
    <script>
        // Global error handler
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('❌ GLOBAL ERROR:', msg, 'at', url, ':', lineNo);
            return false;
        };

        // ===== MODAL TAMBAH DEPARTEMEN - OPEN FUNCTION =====
        // Fungsi ini SELALU membuat instance modal baru untuk menghindari state korup
        window.openTambahDeptModal = function() {
            console.log('🔘 openTambahDeptModal() called');
            
            var modalEl = document.getElementById('modalTambahDept');
            if (!modalEl) {
                console.error('❌ Modal element #modalTambahDept not found!');
                return;
            }
            
            // 1. Pastikan tidak ada backdrop yang tersisa dari sebelumnya
            document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                el.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // 2. Dispose instance lama jika ada
            var existingInstance = bootstrap.Modal.getInstance(modalEl);
            if (existingInstance) {
                console.log('🗑️ Disposing existing modal instance');
                existingInstance.dispose();
            }
            
            // 3. Reset form dan clear container SEBELUM modal dibuka
            var form = document.getElementById('formTambahDept');
            if (form) {
                form.reset();
                console.log('✓ Form reset');
            }
            
            var divContainer = document.getElementById('containerDivisiTambah');
            if (divContainer) {
                divContainer.innerHTML = '';
                console.log('✓ Divisi container cleared');
            }
            
            // 4. Tambah 1 divisi field awal
            if (window.tambahFieldDivisiTambah) {
                window.tambahFieldDivisiTambah();
                console.log('✓ Initial divisi field added');
            }
            
            // 5. Buat instance BARU dan tampilkan modal
            console.log('🆕 Creating new modal instance');
            var modal = new bootstrap.Modal(modalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
            modal.show();
            console.log('✅ Modal opened successfully');
        };

        // Load dan render departemen table via AJAX
        window.loadDepartemenTable = function() {
            console.log('📡 Loading departemen table...');
            
            $.ajax({
                url: '/api/departments?_t=' + Date.now(), // Add timestamp to bypass cache
                type: 'GET',
                cache: false, // Disable cache
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('✅ Departments loaded:', response.data);
                    
                    if (response.success && response.data) {
                        var tbody = document.querySelector('#tableDepartemen tbody');
                        
                        if (!tbody) {
                            console.error('❌ Table body not found');
                            return;
                        }
                        
                        // Clear existing rows
                        tbody.innerHTML = '';
                        
                        if (response.data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data departemen</td></tr>';
                            return;
                        }
                        
                        // Render rows
                        response.data.forEach((dept, index) => {
                            var row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><strong>${dept.name}</strong></td>
                                    <td class="text-center"><span class="badge bg-info">${dept.divisions_count || 0}</span></td>
                                    <td class="text-center"><span class="badge bg-success">${dept.employees_count || 0}</span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning me-1" onclick="window.editDepartemen(${dept.id}, '${dept.name}')" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="window.hapusDepartemen(${dept.id}, '${dept.name}')" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                        
                        console.log('✅ Table rendered with', response.data.length, 'departments');
                    }
                },
                error: function(xhr) {
                    console.error('❌ Error loading departments:', xhr);
                    var tbody = document.querySelector('#tableDepartemen tbody');
                    if (tbody) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data</td></tr>';
                    }
                }
            });
        };

        // Load both Karyawan and Departemen tables sekaligus
        window.loadBothTables = function() {
            console.log('🔄 🔄 🔄 loadBothTables() CALLED! 🔄 🔄 🔄');
            console.log('   Reloading Karyawan table...');
            if (typeof window.loadKaryawanTable === 'function') window.loadKaryawanTable();
            console.log('   Reloading Departemen table...');
            window.loadDepartemenTable();
            console.log('✅ Both tables reload initiated');
        };

        // Simpan department dengan semua divisions & positions
        window.simpanDept = function() {
            var nama = document.getElementById('namaDeptTambah').value.trim();
            var divisiWrappers = document.querySelectorAll('#containerDivisiTambah .divisi-wrapper');

            if (!nama) {
                Swal.fire({title: 'Validasi!', text: 'Nama departemen harus diisi', icon: 'warning'});
                return;
            }

            if (divisiWrappers.length === 0) {
                Swal.fire({title: 'Validasi!', text: 'Minimal 1 divisi harus diisi', icon: 'warning'});
                return;
            }

            // Collect dan validate data
            var divisiData = [];
            var isValid = true;

            divisiWrappers.forEach(function(divWrapper, divIndex) {
                var divName = divWrapper.querySelector('.divisi-name-input').value.trim();

                if (!divName) {
                    Swal.fire({title: 'Validasi!', text: 'Nama divisi harus diisi', icon: 'warning'});
                    isValid = false;
                    return;
                }

                var positionFields = divWrapper.querySelectorAll('.position-field .position-name-input');
                var positions = [];

                positionFields.forEach(function(posInput) {
                    var posName = posInput.value.trim();
                    if (posName) {
                        positions.push(posName);
                    }
                });

                if (positions.length === 0) {
                    Swal.fire({
                        title: 'Validasi!',
                        text: 'Divisi "' + divName + '" harus memiliki minimal 1 jabatan',
                        icon: 'warning'
                    });
                    isValid = false;
                    return;
                }

                divisiData.push({
                    name: divName,
                    positions: positions
                });
            });

            if (!isValid || divisiData.length === 0) return;

            // Log data yang akan dikirim
            var payloadData = {name: nama};
            console.log('📦 Sending payload:', JSON.stringify(payloadData));
            console.log('✅ Departemen nama:', nama);
            console.log('📋 Divisi data:', divisiData);

            // Buat departemen
            $.ajax({
                url: '/api/departments',
                type: 'POST',
                data: JSON.stringify(payloadData),
                contentType: 'application/json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.data && response.data.id) {
                        var deptId = response.data.id;
                        
                        // Create divisions & positions
                        var allPromises = [];

                        divisiData.forEach(function(divData) {
                            var divPromise = $.ajax({
                                url: '/api/divisions',
                                type: 'POST',
                                data: JSON.stringify({name: divData.name, department_id: deptId}),
                                contentType: 'application/json',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                            });

                            allPromises.push(
                                divPromise.then(function(divResponse) {
                                    if (divResponse.data && divResponse.data.id) {
                                        var divId = divResponse.data.id;
                                        var posPromises = [];
                                        divData.positions.forEach(function(posName) {
                                            posPromises.push(
                                                $.ajax({
                                                    url: '/api/positions',
                                                    type: 'POST',
                                                    data: JSON.stringify({name: posName, division_id: divId}),
                                                    contentType: 'application/json',
                                                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                                })
                                            );
                                        });
                                        return $.when.apply($, posPromises);
                                    }
                                })
                            );
                        });

                        // Tunggu semua selesai
                        $.when.apply($, allPromises).done(function() {
                            console.log('✅ All AJAX operations completed successfully');
                            
                            // 1. Tutup Modal dengan DISPOSE untuk membersihkan instance
                            var modalEl = document.getElementById('modalTambahDept');
                            var modalInstance = bootstrap.Modal.getInstance(modalEl);
                            
                            if (modalInstance) {
                                console.log('🔄 Disposing modal instance...');
                                // Hide dan dispose modal instance agar tidak ada state korup
                                modalInstance.hide();
                                
                                // Tunggu transisi selesai sebelum dispose
                                modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                                    modalEl.removeEventListener('hidden.bs.modal', onHidden);
                                    modalInstance.dispose();
                                    console.log('✅ Modal instance disposed');
                                }, { once: true });
                            } else {
                                console.log('⚠️ No modal instance found, just hiding element');
                                modalEl.classList.remove('show');
                                modalEl.style.display = 'none';
                            }
                            
                            // 2. Cleanup SETELAH modal transition selesai (300ms adalah default Bootstrap transition)
                            setTimeout(function() {
                                // Force cleanup jika masih ada sisa backdrop
                                document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                                    el.remove();
                                });
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';
                                console.log('✅ Backdrop cleanup completed');
                            }, 350);

                            // 3. Reload tabel
                            window.loadBothTables();

                            // 4. Tampilkan notifikasi sukses
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Departemen, divisi, dan jabatan berhasil disimpan',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                timer: 1500,
                                timerProgressBar: true
                            });

                        }).fail(function(xhr) {
                            // Handle error simpan detail
                            console.error('❌ Error details:', xhr);
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menyimpan detail data',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }).always(function() {
                            // Reset tombol (jika pakai loading)
                            // btnSimpan.disabled = false;
                            // btnSimpan.innerHTML = originalText;
                        });
                    }
                },
                error: function(xhr) {
                    console.error('❌ Error creating department:', xhr);
                    console.error('Status:', xhr.status, xhr.statusText);
                    console.error('Response:', xhr.responseText);
                    
                    var errorMsg = 'Gagal membuat departemen';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                        if (response.errors) {
                            console.error('Validation errors:', response.errors);
                            errorMsg += '\n' + JSON.stringify(response.errors);
                        }
                    } catch(e) {
                        console.error('Could not parse error response');
                    }
                    
                    Swal.fire({
                        title: 'Gagal!',
                        text: errorMsg,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                    // btnSimpan.disabled = false;
                    // btnSimpan.innerHTML = originalText;
                }
            });
        };

        {{-- ======================================================================== --}}
        {{-- SECTION: JAVASCRIPT - DEPARTEMEN FUNCTIONS --}}
        {{-- Functions untuk CRUD Departemen: editDept, updateDept, hapusDept, dll --}}
        {{-- ======================================================================== --}}

        // Load dan buka modal edit dengan nested structure
        window.editDept = function(id, nama) {
            console.log('🔧 Edit department:', id, nama);
            
            var deptId = id;
            document.getElementById('editIdDept').value = deptId;
            document.getElementById('editNamaDept').value = nama;
            document.getElementById('containerDivisiEdit').innerHTML = '';
            
            // Load divisions & positions
            $.ajax({
                url: '/api/divisions?department_id=' + deptId,
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    console.log('✅ Divisions loaded:', response.data);
                    
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(division) {
                            window.addExistingDivisionEdit(division.id, division.name);
                        });
                    }
                    
                    // Open modal after loading
                    var modal = new bootstrap.Modal(document.getElementById('modalEditDept'));
                    modal.show();
                },
                error: function(xhr) {
                    console.error('❌ Error loading divisions:', xhr);
                    var modal = new bootstrap.Modal(document.getElementById('modalEditDept'));
                    modal.show();
                }
            });
        };

        // Add existing division ke container (dengan nested positions)
        window.addExistingDivisionEdit = function(divId, divName) {
            var container = document.getElementById('containerDivisiEdit');
            
            var html = `
                <div class="mb-3 p-3 border-2 border-primary rounded bg-light divisi-wrapper-edit" data-div-id="${divId}">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm divisi-name-edit" value="${divName}" data-original="${divName}" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.divisi-wrapper-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Nested Positions Container -->
                    <div class="ms-3 mb-2 p-3 bg-white rounded border">
                        <label class="form-label small fw-bold mb-2">
                            <i class="bi bi-briefcase me-1"></i>Jabatan untuk divisi ini:
                        </label>
                        <div class="positions-container-edit" data-div-id="${divId}">
                            <!-- Positions akan di-load di sini -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="window.tambahFieldPositionEdit(${divId})">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Jabatan
                        </button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
            
            // Load existing positions untuk division ini
            $.ajax({
                url: '/api/positions?division_id=' + divId,
                type: 'GET',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        var posContainer = document.querySelector(`.positions-container-edit[data-div-id="${divId}"]`);
                        response.data.forEach(function(position) {
                            window.addExistingPositionEdit(divId, position.id, position.name, posContainer);
                        });
                    }
                }
            });
        };

        // Add existing position ke container
        window.addExistingPositionEdit = function(divId, posId, posName, container) {
            var html = `
                <div class="mb-2 p-2 bg-light border rounded position-field-edit" data-pos-id="${posId}">
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <input type="text" class="form-control form-control-sm position-name-edit" value="${posName}" data-original="${posName}" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.position-field-edit').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        };

        // ===== FUNGSI UPDATE DEPARTEMEN (DEPRECATED - diganti window.updateDepartemen) =====
        window.updateDept = function() {
            var deptId = document.getElementById('editIdDept').value;
            var deptName = document.getElementById('editNamaDept').value.trim();
            
            if (!deptName) {
                Swal.fire({
                    title: 'Validasi!',
                    text: 'Nama departemen harus diisi',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            console.log('📦 Updating department:', {id: deptId, name: deptName});

            // Update department name
            $.ajax({
                url: '/api/departments/' + deptId,
                type: 'PUT',
                data: JSON.stringify({name: deptName}),
                contentType: 'application/json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    console.log('✅ Department updated');
                    
                    // Process divisions & positions (same like simpanDept)
                    var divWrappers = document.querySelectorAll('#containerDivisiEdit .divisi-wrapper-edit');
                    var allPromises = [];
                    
                    divWrappers.forEach(function(divWrapper) {
                        var divId = divWrapper.getAttribute('data-div-id');
                        var divName = divWrapper.querySelector('.divisi-name-edit').value.trim();
                        
                        if (!divName) return;

                        if (divId.includes('new_')) {
                            // Create new division
                            var divPromise = $.ajax({
                                url: '/api/divisions',
                                type: 'POST',
                                data: JSON.stringify({name: divName, department_id: deptId}),
                                contentType: 'application/json',
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                            });

                            allPromises.push(
                                divPromise.then(function(divResponse) {
                                    if (divResponse.data && divResponse.data.id) {
                                        var newDivId = divResponse.data.id;
                                        console.log('✅ Division created:', newDivId);
                                        
                                        // Create positions untuk division ini
                                        var posFields = divWrapper.querySelectorAll('.position-field-edit .position-name-edit');
                                        var posPromises = [];
                                        
                                        posFields.forEach(function(posInput) {
                                            var posName = posInput.value.trim();
                                            if (posName) {
                                                posPromises.push(
                                                    $.ajax({
                                                        url: '/api/positions',
                                                        type: 'POST',
                                                        data: JSON.stringify({name: posName, division_id: newDivId}),
                                                        contentType: 'application/json',
                                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                                    })
                                                );
                                            }
                                        });
                                        
                                        return $.when.apply($, posPromises.length > 0 ? posPromises : [$.when()]);
                                    }
                                })
                            );
                        } else {
                            // Existing division - process position changes
                            var posFields = divWrapper.querySelectorAll('.position-field-edit');
                            
                            posFields.forEach(function(posField) {
                                var posId = posField.getAttribute('data-pos-id');
                                var posName = posField.querySelector('.position-name-edit').value.trim();
                                var original = posField.querySelector('.position-name-edit').getAttribute('data-original');
                                
                                if (posId.includes('new_') && posName) {
                                    // New position
                                    allPromises.push(
                                        $.ajax({
                                            url: '/api/positions',
                                            type: 'POST',
                                            data: JSON.stringify({name: posName, division_id: divId}),
                                            contentType: 'application/json',
                                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                                        })
                                    );
                                }
                            });
                        }
                    });

                    // Wait for all operations then show success
                    $.when.apply($, allPromises.length > 0 ? allPromises : [$.when()]).done(function() {
                        console.log('✅ All updates completed!');
                        
                        // Close modal
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditDept'));
                        if (modal) modal.hide();
                        
                        // Cleanup backdrop
                        document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                            el.remove();
                        });
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        
                        window.onbeforeunload = null;
                        
                        setTimeout(function() {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Departemen berhasil diupdate',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                timer: 2000,
                                timerProgressBar: true,
                                didClose: function() {
                                    window.loadBothTables();
                                }
                            });
                        }, 100);
                    }).fail(function(xhr) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal update data',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal update departemen',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        };

        window.hapusDept = function(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin hapus departemen: ' + nama + ' ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Execute delete
                    $.ajax({
                        url: '/api/departments/' + id,
                        type: 'DELETE',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function() {
                            // Bersihkan backdrop
                            document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                                el.remove();
                            });
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            
                            // Matikan beforeunload
                            window.onbeforeunload = null;
                            
                            // Tampilkan notifikasi
                            setTimeout(function() {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Departemen berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonColor: '#28a745',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    didClose: function() {
                                        // Reload both tables via AJAX
                                        window.loadBothTables();
                                    }
                                });
                            }, 100);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal hapus departemen: ' + (xhr.responseJSON?.message || 'Error'),
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        };

        window.confirmHapusDept = function() {
            var id = window.deptIdHapus;
            $.ajax({
                url: '/api/departments/' + id,
                type: 'DELETE',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function() {
                    alert('✅ Departemen berhasil dihapus');
                    location.reload();
                },
                error: function(xhr) {
                    alert('❌ Gagal hapus: ' + (xhr.responseJSON?.message || 'Error'));
                }
            });
        };

        // ===== GLOBAL FUNCTIONS (Outside document.ready for HTML onclick access) =====
        window.openTambahKaryawanModal = function() {
            console.log('🔘 openTambahKaryawanModal dipanggil');
            console.log('📍 Current URL:', window.location.href);
            console.log('� APP_URL dari server:', '{{ config("app.url") }}');
            console.log('🔐 CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
            console.log('jQuery version:', $.fn.jquery);
            
            try {
                $('#formTambahKaryawan')[0].reset();
                
                // Get dropdown elements
                var deptSelect = $('#departemenKaryawan');
                var divisionSelect = $('#divisiKaryawan');
                var positionSelect = $('#jabatanKaryawan');
                
                console.log('✅ Form elements found:');
                console.log('   - departemenKaryawan:', deptSelect.length ? 'FOUND' : 'NOT FOUND');
                console.log('   - divisiKaryawan:', divisionSelect.length ? 'FOUND' : 'NOT FOUND');
                console.log('   - jabatanKaryawan:', positionSelect.length ? 'FOUND' : 'NOT FOUND');
                
                // Clear dropdowns
                deptSelect.html('<option value="">-- Loading Departemen... --</option>');
                divisionSelect.html('<option value="">-- Pilih Divisi --</option>');
                positionSelect.html('<option value="">-- Pilih Jabatan --</option>');
                console.log('✅ Dropdowns cleared, now fetching departments...');
                
                // Load departments dengan URL yang benar
                var apiUrl = '{{ url("/api/dropdowns/departments") }}';
                console.log('📥 AJAX CALL START:', apiUrl);
                console.log('📥 Full URL will be:', apiUrl);
                $.ajax({
                    url: apiUrl,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('✅ AJAX SUCCESS');
                        console.log('Full response:', response);
                        console.log('Response.success:', response.success);
                        console.log('Response.data:', response.data);
                        
                        if (response && response.data && Array.isArray(response.data)) {
                            console.log(`📊 Found ${response.data.length} departments`);
                            
                            if (response.data.length === 0) {
                                console.warn('⚠️ No departments found');
                                deptSelect.html('<option value="">-- Tidak ada departemen --</option>');
                            } else {
                                deptSelect.html('<option value="">-- Pilih Departemen --</option>');
                                response.data.forEach(function(dept, index) {
                                    console.log(`Adding option [${index+1}]: ID=${dept.id}, Name=${dept.name}`);
                                    deptSelect.append(`<option value="${dept.id}">${dept.name}</option>`);
                                });
                                console.log('✅ Total dropdown options:', deptSelect.find('option').length);
                            }
                        } else {
                            console.error('❌ Invalid response format - response.data not array');
                            deptSelect.html('<option value="">-- Invalid response format --</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ AJAX ERROR');
                        console.error('   Status:', xhr.status, xhr.statusText);
                        console.error('   Error text:', error);
                        console.error('   Response:', xhr.responseText.substring(0, 200));
                        deptSelect.html(`<option value="">-- Error ${xhr.status} --</option>`);
                    },
                    complete: function() {
                        console.log('📍 AJAX request complete');
                    }
                });
                
            } catch (e) {
                console.error('🔴 Exception caught:', e.message);
                console.error('Stack:', e.stack);
            }
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('modalTambahKaryawan'));
            modal.show();
            console.log('✅ Modal displayed');
        };

        {{-- ======================================================================== --}}
        {{-- SECTION: JAVASCRIPT - INITIALIZATION & KARYAWAN FUNCTIONS --}}
        {{-- ======================================================================== --}}

        $(document).ready(function() {
            console.log('📄 Document ready - jQuery loaded:', typeof jQuery !== 'undefined');
            
            // ===== SETUP GLOBAL AJAX CONFIG =====
            var baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '').replace(/\/public.*$/, '/public');
            console.log('🌐 Base URL set to:', baseUrl);
            
            $.ajaxSetup({
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                }
            });
            
            // Helper function untuk auto-close alert setelah beberapa detik
            function autoCloseAlert(alertSelector, duration) {
                setTimeout(function() {
                    $(alertSelector).fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, duration || 3000); // Default 3 detik
            }
            
            // Check for hash in URL and activate corresponding tab
            function activateTabFromHash() {
                var hash = window.location.hash;
                if (hash) {
                    // Remove '#' from hash
                    var tabId = hash.substring(1);
                    // Find the corresponding tab button
                    var tabButton = document.getElementById(tabId + '-tab');
                    if (tabButton) {
                        // Activate the tab using Bootstrap 5
                        var tab = new bootstrap.Tab(tabButton);
                        tab.show();
                    }
                }
            }

            // Activate tab on page load
            activateTabFromHash();

            // Update hash when tab is clicked
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                var hash = $(e.target).attr('data-bs-target');
                history.pushState(null, null, hash);
            });

            // Activate tab when hash changes (browser back/forward)
            $(window).on('hashchange', function() {
                activateTabFromHash();
            });

            // ===== FILTER DROPDOWN KARYAWAN - SIMPLE VERSION =====
            // Fungsi untuk load filter departemen
            function loadFilterDepartemen() {
                $.ajax({
                    url: '/api/departments',
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            var select = $('#filterDepartemenKaryawan');
                            select.find('option:not(:first)').remove();
                            
                            response.data.forEach(function(dept) {
                                select.append('<option value="' + dept.id + '">' + dept.name + '</option>');
                            });
                            
                            console.log('✅ Filter Departemen loaded:', response.data.length, 'items');
                        }
                    },
                    error: function(xhr) {
                        console.error('❌ Error loading departments:', xhr.status);
                    }
                });
            }
            
            // Fungsi untuk load filter divisi
            function loadFilterDivisi() {
                $.ajax({
                    url: '/api/divisions',
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            var select = $('#filterDivisiKaryawan');
                            select.find('option:not(:first)').remove();
                            
                            response.data.forEach(function(div) {
                                select.append(
                                    '<option value="' + div.id + '" data-dept-id="' + div.department_id + '">' + 
                                    div.name + 
                                    '</option>'
                                );
                            });
                            
                            console.log('✅ Filter Divisi loaded:', response.data.length, 'items');
                        }
                    },
                    error: function(xhr) {
                        console.error('❌ Error loading divisions:', xhr.status);
                    }
                });
            }
            
            // Event handler: Ketika filter departemen berubah  
            $('#filterDepartemenKaryawan').on('change', function() {
                var deptId = $(this).val();
                var divSelect = $('#filterDivisiKaryawan');
                
                // Reset filter divisi
                divSelect.val('');
                
                if (!deptId) {
                    // Tampilkan semua divisi
                    divSelect.find('option').show();
                } else {
                    // Filter divisi berdasarkan departemen yang dipilih
                    divSelect.find('option').each(function() {
                        var optDeptId = $(this).attr('data-dept-id');
                        if (!optDeptId || optDeptId == deptId) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
                
                // Reload table with new filter
                window.loadBothTables();
            });
            
            // Event handler: Ketika filter divisi berubah
            $('#filterDivisiKaryawan').on('change', function() {
                console.log('Filter divisi changed:', $(this).val());
                window.loadBothTables();
            });
            
            // Load filter saat halaman ready
            loadFilterDepartemen();
            loadFilterDivisi();
            
            // Function untuk attach event handler untuk department change (untuk modal)
            function attachDepartmentChangeHandler() {
                var elem = $('#departemenKaryawan');
                console.log('Found element:', elem.length > 0 ? 'YES' : 'NO');
                
                // Hapus handler lama jika ada
                elem.off('change');
                
                // Attach handler baru
                elem.on('change', function(e) {
                    var departmentId = $(this).val();
                    console.log('🔄 Department changed in Tambah Karyawan. Dept ID:', departmentId);
                    console.log('Selected text:', $(this).find('option:selected').text());
                    
                    var jabatanSelect = $('#jabatanKaryawan');
                    var divisiSelect = $('#divisiKaryawan');
                    
                    console.log('Jabatan select found:', jabatanSelect.length > 0 ? 'YES' : 'NO');
                    console.log('Divisi select found:', divisiSelect.length > 0 ? 'YES' : 'NO');

                    if (!departmentId) {
                        console.log('⚠️ No department selected');
                        jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                        divisiSelect.html('<option value="">-- Pilih Divisi --</option>');
                        return;
                    }

                    // Load positions dari API berdasarkan division_id (bukan department_id)
                    console.log('📥 Loading positions for division:', divisionId);
                    // Positions akan diload setelah division dipilih via AJAX
                    // Lihat function: loadPositionsByDivision()

                    // Load divisions dari API berdasarkan department_id
                    console.log('📥 Loading divisions for dept:', departmentId);
                    $.ajax({
                        url: '/api/divisions?department_id=' + departmentId,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ Divisions loaded:', response);
                            divisiSelect.html('<option value="">-- Pilih Divisi --</option>');
                            
                            if (response.success && response.data && response.data.length > 0) {
                                response.data.forEach(function(division) {
                                    divisiSelect.append('<option value="' + division.id + '">' + division.name + '</option>');
                                });
                                console.log('✅ Added ' + response.data.length + ' divisions to dropdown');
                            } else {
                                console.log('⚠️ No divisions found for this department');
                                divisiSelect.html('<option value="">-- Tidak ada Divisi --</option>');
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading divisions:', xhr);
                            divisiSelect.html('<option value="">-- Error loading Divisi --</option>');
                        }
                    });
                });
                
                console.log('✅ Department change handler attached successfully');
            }
            
            // ===== SIMPLE DROPDOWN LOADERS FOR TAMBAH KARYAWAN =====
            
            // Function 1: Load Departments (dipanggil saat page load)
            function loadDepartmentsDropdown() {
                console.log('📦 Loading departments...');
                $.ajax({
                    url: '/api/departments',
                    method: 'GET',
                    success: function(response) {
                        console.log('✅ Departments response:', response);
                        if (response.success && response.data) {
                            const select = $('#departemenKaryawan');
                            select.find('option:not(:first)').remove(); // Clear except placeholder
                            
                            response.data.forEach(dept => {
                                select.append(`<option value="${dept.id}">${dept.name}</option>`);
                            });
                            
                            console.log(`✅ Loaded ${response.data.length} departments into dropdown`);
                            console.log('   Total options:', select.find('option').length);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Failed to load departments:', error);
                    }
                });
            }
            
            // Function 2: Load Divisions by Department ID
            function loadDivisionsByDepartment(deptId) {
                console.log('📦 Loading divisions for department:', deptId);
                const divSelect = $('#divisiKaryawan');
                const posSelect = $('#jabatanKaryawan');
                
                // Clear divisions and positions
                divSelect.find('option:not(:first)').remove();
                posSelect.find('option:not(:first)').remove();
                
                if (!deptId) return;
                
                $.ajax({
                    url: '/api/dropdowns/divisions',
                    method: 'GET',
                    data: { department_id: deptId },
                    success: function(response) {
                        console.log('✅ Divisions response:', response);
                        if (response.success && response.data) {
                            response.data.forEach(div => {
                                divSelect.append(`<option value="${div.id}">${div.name}</option>`);
                            });
                            console.log(`✅ Loaded ${response.data.length} divisions`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Failed to load divisions:', error);
                    }
                });
            }
            
            // Function 3: Load Positions by Division ID
            function loadPositionsByDivisionId(divId) {
                console.log('📦 Loading positions for division:', divId);
                const posSelect = $('#jabatanKaryawan');
                
                // Clear positions
                posSelect.find('option:not(:first)').remove();
                
                if (!divId) return;
                
                $.ajax({
                    url: '/api/dropdowns/positions',
                    method: 'GET',
                    data: { division_id: divId },
                    success: function(response) {
                        console.log('✅ Positions response:', response);
                        if (response.success && response.data) {
                            response.data.forEach(pos => {
                                posSelect.append(`<option value="${pos.id}">${pos.name}</option>`);
                            });
                            console.log(`✅ Loaded ${response.data.length} positions`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Failed to load positions:', error);
                    }
                });
            }
            
            // Event Handler: When Department changes (FOR MODAL EDIT - #departemenKaryawan)
            $('#departemenKaryawan').off('change').on('change', function() {
                const deptId = $(this).val();
                console.log('🔄 Department changed to:', deptId);
                loadDivisionsByDepartment(deptId);
            });
            
            // Event Handler: When Division changes (FOR MODAL EDIT - #divisiKaryawan)
            $('#divisiKaryawan').off('change').on('change', function() {
                const divId = $(this).val();
                console.log('🔄 Division changed to:', divId);
                loadPositionsByDivisionId(divId);
            });
            
            // REMOVED: Old AJAX handler for modalTambahKaryawan
            // Now using cascade filter with server-side data (see line ~3480)

            // ===== CASCADING DROPDOWN HELPERS (FOR FILTER) =====
            // Load divisions based on selected department
            window.loadDivisiByDepartment = function(departmentId, targetDivisiId, targetJabatanId = null) {
                const divisiSelect = $(targetDivisiId);
                const jabatanSelect = targetJabatanId ? $(targetJabatanId) : null;
                
                // Clear divisions and positions
                divisiSelect.find('option:not(:first)').remove();
                if (jabatanSelect) {
                    jabatanSelect.find('option:not(:first)').remove();
                }
                
                if (!departmentId) return;
                
                const apiUrl = `${window.location.origin}/api/dropdowns/divisions?department_id=${departmentId}`;
                console.log('📡 Fetching divisions from:', apiUrl);
                
                fetch(apiUrl)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data) {
                        data.data.forEach(division => {
                            divisiSelect.append(`<option value="${division.id}">${division.name}</option>`);
                        });
                        console.log(`✅ Divisions loaded for department ${departmentId}:`, data.data.length);
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading divisions:', error);
                });
            };
            
            // Load positions based on selected division
            window.loadJabatanByDivision = function(divisionId, targetJabatanId) {
                const jabatanSelect = $(targetJabatanId);
                jabatanSelect.find('option:not(:first)').remove();
                
                if (!divisionId) return;
                
                const apiUrl = `${window.location.origin}/api/dropdowns/positions?division_id=${divisionId}`;
                console.log('📡 Fetching positions from:', apiUrl);
                
                fetch(apiUrl)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data) {
                        data.data.forEach(position => {
                            jabatanSelect.append(`<option value="${position.id}">${position.name}</option>`);
                        });
                        console.log(`✅ Positions loaded for division ${divisionId}:`, data.data.length);
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading positions:', error);
                });
            };

            // ===== DEBOUNCE HELPER FUNCTION =====
            // Prevent excessive API calls - wait 300ms after user stops typing
            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            // Load and render karyawan table - FIXED
            window.loadKaryawanTable = function() {
                console.log('🔄 loadKaryawanTable() called');
                const tableBody = document.getElementById('tableKaryawanBody');
                
                if (!tableBody) {
                    console.error('❌ tableKaryawanBody not found!');
                    return;
                }
                
                // Baca nilai filter
                var deptId = $('#filterDepartemenKaryawan').val();
                var divId = $('#filterDivisiKaryawan').val();
                var search = $('#searchKaryawan').val();
                
                console.log('📡 Fetching from /api/employees...');
                console.log('   Filters - dept:', deptId, 'div:', divId, 'search:', search);
                
                $.ajax({
                    url: '/api/employees',
                    type: 'GET',
                    data: {
                        department_id: deptId || '',
                        division_id: divId || '',
                        search: search || ''
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('✅ API Response:', response);
                        
                        if (response.success && response.data) {
                            console.log('📦 Data count:', response.data.length);
                            
                            window.renderKaryawanTable(response.data, tableBody);
                        } else {
                            console.warn('⚠️ No data in response');
                            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data karyawan</td></tr>';
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ AJAX Error:', error);
                        console.error('   Status:', xhr.status);
                        console.error('   Response:', xhr.responseText);
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error: ' + error + '</td></tr>';
                    }
                });
            };

            // Render karyawan table - SIMPLIFIED
            window.renderKaryawanTable = function(data, tableBody) {
                console.log('🎨 Rendering table with', data.length, 'rows');
                
                if (!tableBody) {
                    console.error('❌ tableBody is null!');
                    return;
                }
                
                tableBody.innerHTML = '';
                
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Belum ada data karyawan</td></tr>';
                    console.log('📋 No data to display');
                    return;
                }

                // Render each row
                data.forEach((item, index) => {
                    console.log('  Row ' + (index + 1) + ':', item.nik, '-', item.name);
                    
                    const employeeName = item.name || item.nama_karyawan || 'N/A';
                    const statusBadge = item.status === 'Aktif' 
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Non-Aktif</span>';

                    const deptName = item.department ? item.department.name : '-';
                    const divName = item.division ? item.division.name : '-';
                    const deptDivisi = `<small>${deptName} / ${divName}</small>`;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center"><strong>${item.nik}</strong></td>
                        <td>${employeeName}</td>
                        <td>${deptDivisi}</td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-edit-karyawan" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditEmployee"
                                        data-employee-nik="${item.nik}" 
                                        title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-hapus-karyawan" 
                                        data-nik="${item.nik}" 
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                
                console.log('✅ Table rendered successfully');
            };

                // Helper function untuk load divisions saat edit modal
                function loadDivisionsForEdit(departmentId, divisionId, positionId) {
                    console.log('📊 Loading divisions for edit - Dept:', departmentId, 'Div:', divisionId, 'Pos:', positionId);
                    
                    var divSelect = document.getElementById('editDivisiKaryawan');
                    var posSelect = document.getElementById('editJabatanKaryawan');
                    
                    divSelect.innerHTML = '<option value="">-- Pilih Divisi --</option>';
                    posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                    
                    if (!departmentId) {
                        return;
                    }
                    
                    // Load divisions
                    $.ajax({
                        url: '/api/divisions?department_id=' + departmentId,
                        type: 'GET',
                        success: function(response) {
                            console.log('✅ Divisions loaded:', response.data);
                            
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(div) {
                                    var option = document.createElement('option');
                                    option.value = div.id;
                                    option.textContent = div.name;
                                    divSelect.appendChild(option);
                                });
                                
                                // Set division value
                                divSelect.value = divisionId || '';
                                console.log('📍 Division set to:', divisionId);
                                
                                // Load positions untuk division ini
                                if (divisionId) {
                                    loadPositionsForEdit(divisionId, positionId);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading divisions:', xhr);
                        }
                    });
                }
                
                // Helper function untuk load positions saat edit modal
                function loadPositionsForEdit(divisionId, positionId) {
                    console.log('🎯 Loading positions for edit - Div:', divisionId, 'Pos:', positionId);
                    
                    var posSelect = document.getElementById('editJabatanKaryawan');
                    posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
                    
                    if (!divisionId) {
                        return;
                    }
                    
                    // Load positions
                    $.ajax({
                        url: '/api/positions?division_id=' + divisionId,
                        type: 'GET',
                        success: function(response) {
                            console.log('✅ Positions loaded:', response.data);
                            
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(pos) {
                                    var option = document.createElement('option');
                                    option.value = pos.id;
                                    option.textContent = pos.name;
                                    posSelect.appendChild(option);
                                });
                                
                                // Set position value
                                posSelect.value = positionId || '';
                                console.log('📍 Position set to:', positionId);
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Error loading positions:', xhr);
                        }
                    });
                }
                
            // ===== MODAL TAMBAH KARYAWAN EVENT HANDLERS - Backdrop Cleanup =====
            var modalTambahKaryawan = document.getElementById('modalTambahKaryawan');
            if (modalTambahKaryawan) {
                // Dropdown departemen sudah di-render langsung di HTML, tidak perlu load via AJAX lagi
                
                // Event saat modal DITUTUP
                modalTambahKaryawan.addEventListener('hidden.bs.modal', function() {
                    console.log('🔓 Modal Tambah Karyawan ditutup');
                    
                    // Reset form
                    var form = document.getElementById('formTambahKaryawan');
                    if (form) {
                        form.reset();
                    }
                    
                    // Force cleanup backdrop dan body classes
                    var backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    
                    // Remove modal-open dari body
                    document.body.classList.remove('modal-open');
                    
                    // Remove inline styles pada body
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    console.log('✅ Backdrop dan body classes cleaned up');
                });
            }

            // editKaryawan and hapusKaryawan are now defined globally at the top of the page

            // Handle reset filter button
            $('#btnResetFilter').on('click', function() {
                console.log('🔄 Reset filter clicked');
                $('#filterDepartemenKaryawan').val('');
                $('#filterDivisiKaryawan').val('');
                $('#searchKaryawan').val('');
                window.loadBothTables();
            });

            // Handle search input with debounce (300ms delay)
            $('#searchKaryawan').on('keyup', debounce(function() {
                var searchTerm = $(this).val().toLowerCase();
                console.log('🔍 Search:', searchTerm);
                window.loadBothTables();
            }, 300));

            // Initialize tableDepartemen (data sudah di-render dari server via Blade)
            var tableDepartemen = $('#tableDepartemen').DataTable({
                language: {
                    url: '{{ asset("assets/datatables/i18n/id.json") }}',
                    "decimal": ",",
                    "emptyTable": "Tidak ada data tersedia",
                    "info": "Menampilkan _START_ ke _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 ke 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "thousands": ".",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada entri yang cocok ditemukan"
                },
                pageLength: 10,
                order: []
            });

            // Assign to window untuk akses global
            window.tableDepartemen = tableDepartemen;

            // Filter departemen dropdown
            $('#filterDepartemenMaster').on('change', function() {
                var selectedDept = $(this).val();
                tableDepartemen.column(1).search(selectedDept).draw();
            });

            $('#tableJabatan').DataTable({
                language: {
                    url: '{{ asset("assets/datatables/i18n/id.json") }}',
                    "decimal": ",",
                    "emptyTable": "Tidak ada data tersedia",
                    "info": "Menampilkan _START_ ke _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 ke 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ total entri)",
                    "infoPostFix": "",
                    "thousands": ".",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada entri yang cocok ditemukan"
                },
                pageLength: 10
            });

            // Positions akan dimuat dinamis dari API berdasarkan Division yang dipilih

            // Button handlers
            // Handle Tambah Karyawan button click (Backup jQuery handler)
            $('#btnTambahKaryawan').on('click', function(e) {
                e.preventDefault();
                console.log('🔘 jQuery handler: Tombol Tambah Karyawan diklik');
                openTambahKaryawanModal();
            });

            // Handle Department dropdown change - populate Division
            $(document).on('change', '#departemenKaryawan', function() {
                var deptId = $(this).val();
                var divisionSelect = $('#divisiKaryawan');
                var positionSelect = $('#jabatanKaryawan');
                
                console.log('🔄 Department changed to:', deptId);
                
                // Clear division dan position
                divisionSelect.html('<option value="">-- Pilih Divisi --</option>');
                positionSelect.html('<option value="">-- Pilih Jabatan --</option>');
                
                if (deptId) {
                    // Load divisions untuk department ini
                    var divisionsUrl = '{{ url("/api/dropdowns/divisions") }}?department_id=' + deptId;
                    console.log('📡 Calling API:', divisionsUrl);
                    $.ajax({
                        url: divisionsUrl,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ Divisions loaded for dept', deptId, ':', response);
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(division) {
                                    divisionSelect.append('<option value="' + division.id + '">' + division.name + '</option>');
                                    console.log('➕ Added division:', division.name);
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Error loading divisions:', error, xhr);
                        }
                    });
                }
            });

            // Handle Division dropdown change - populate Position
            $(document).on('change', '#divisiKaryawan', function() {
                var divisionId = $(this).val();
                var positionSelect = $('#jabatanKaryawan');
                
                console.log('🔄 Division changed to:', divisionId);
                
                // Clear position
                positionSelect.html('<option value="">-- Pilih Jabatan --</option>');
                
                if (divisionId) {
                    var positionsUrl = '{{ url("/api/dropdowns/positions") }}?division_id=' + divisionId;
                    console.log('📡 Calling API:', positionsUrl);
                    
                    // Load positions untuk division ini
                    $.ajax({
                        url: positionsUrl,
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('✅ API Response:', response);
                            
                            if (response.success && response.data && response.data.length > 0) {
                                console.log('📊 Total positions:', response.data.length);
                                response.data.forEach(function(position) {
                                    positionSelect.append('<option value="' + position.id + '">' + position.name + '</option>');
                                    console.log('➕ Added position:', position.id, '-', position.name);
                                });
                            } else {
                                console.warn('⚠️ No positions found for division:', divisionId);
                                positionSelect.html('<option value="">-- Tidak ada Jabatan --</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            positionSelect.html('<option value="">-- Error loading positions --</option>');
                        }
                    });
                } else {
                    console.log('⚠️ No division selected');
                }
            });

            // Reset form ketika modal ditutup
            $('#modalTambahKaryawan').on('hide.bs.modal', function() {
                $('#formTambahKaryawan')[0].reset();
            });

            // Handle edit button click dengan delegated event untuk dynamic content
            $(document).on('click', '.btn-edit-karyawan', function() {
                var employeeNik = $(this).attr('data-employee-nik');
                console.log('🔧 Edit button clicked for:', employeeNik);
                
                // Fetch employee data from API
                $.ajax({
                    url: '/api/employees/' + employeeNik,
                    type: 'GET',
                    success: function(response) {
                        var emp = response.data;
                        console.log('📥 Employee data fetched:', emp);
                        
                        // Populate edit modal with basic data
                        $('#editKaryawanId').val(emp.nik);
                        $('#editIdKaryawan').val(emp.nik);
                        $('#editNamaKaryawan').val(emp.name || emp.nama_karyawan);
                        $('#editEmailKaryawan').val(emp.email);
                        $('#editPasswordKaryawan').val('');
                        
                        // Set department first
                        $('#editDepartemenKaryawan').val(emp.department_id);
                        
                        // Load divisions for this department, then set division
                        if (emp.department_id) {
                            const divUrl = `${window.location.origin}/api/dropdowns/divisions?department_id=${emp.department_id}`;
                            fetch(divUrl)
                            .then(response => {
                                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                                return response.json();
                            })
                            .then(data => {
                                if (data.success && data.data) {
                                    const divSelect = $('#editDivisiKaryawan');
                                    divSelect.find('option:not(:first)').remove();
                                    data.data.forEach(division => {
                                        divSelect.append(`<option value="${division.id}">${division.name}</option>`);
                                    });
                                    
                                    // Set division value
                                    if (emp.division_id) {
                                        divSelect.val(emp.division_id);
                                        
                                        // Load positions for this division, then set position
                                        const posUrl = `${window.location.origin}/api/dropdowns/positions?division_id=${emp.division_id}`;
                                        return fetch(posUrl);
                                    }
                                }
                            })
                            .then(response => response ? response.json() : null)
                            .then(data => {
                                if (data && data.success && data.data) {
                                    const posSelect = $('#editJabatanKaryawan');
                                    posSelect.find('option:not(:first)').remove();
                                    data.data.forEach(position => {
                                        posSelect.append(`<option value="${position.id}">${position.name}</option>`);
                                    });
                                    
                                    // Set position value
                                    if (emp.position_id) {
                                        posSelect.val(emp.position_id);
                                    }
                                    
                                    console.log('✅ All dropdowns loaded and values set');
                                }
                            })
                            .catch(error => {
                                console.error('❌ Error loading dropdowns:', error);
                            });
                        }
                        
                        // Set status radio button dengan benar
                        if (emp.status === 'Aktif') {
                            $('#editStatusAktif').prop('checked', true);
                            $('#editStatusNonAktif').prop('checked', false);
                        } else if (emp.status === 'Non-Aktif') {
                            $('#editStatusNonAktif').prop('checked', true);
                            $('#editStatusAktif').prop('checked', false);
                        }
                        
                        // Fetch and set competency level
                        $.ajax({
                            url: '/api/competencies?nik=' + employeeNik,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(competencyResponse) {
                                if (competencyResponse.success && competencyResponse.data && competencyResponse.data.length > 0) {
                                    $('#editLevelKompetensi').val(competencyResponse.data[0].level);
                                } else {
                                    $('#editLevelKompetensi').val('');
                                }
                            },
                            error: function() {
                                $('#editLevelKompetensi').val('');
                            }
                        });
                        
                        // Show modal
                        console.log('🔓 Opening edit modal for:', employeeNik);
                        $('#modalEditKaryawan').modal('show');
                    },
                    error: function(xhr) {
                        console.error('❌ Error fetching employee:', xhr);
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal memuat data karyawan',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Update handler moved to inline script near modal edit for better performance

            // ===== EVENT HANDLERS FOR EDIT KARYAWAN CASCADING DROPDOWNS =====
            $('#editDepartemenKaryawan').off('change').on('change', function() {
                const deptId = $(this).val();
                console.log('🔄 Department changed (Edit):', deptId);
                
                // Clear and load divisions and positions
                $('#editDivisiKaryawan').val('').find('option:not(:first)').remove();
                $('#editJabatanKaryawan').val('').find('option:not(:first)').remove();
                
                if (deptId) {
                    window.loadDivisiByDepartment(deptId, '#editDivisiKaryawan', '#editJabatanKaryawan');
                }
            });
            
            $('#editDivisiKaryawan').off('change').on('change', function() {
                const divisionId = $(this).val();
                console.log('🔄 Division changed (Edit):', divisionId);
                
                // Clear and load positions
                $('#editJabatanKaryawan').val('').find('option:not(:first)').remove();
                
                if (divisionId) {
                    window.loadJabatanByDivision(divisionId, '#editJabatanKaryawan');
                }
            });
            
            // Function untuk load positions by division di edit modal
            function loadPositionsByDivisionEdit(divisionId) {
                var jabatanSelect = $('#editJabatanKaryawan');
                
                if (!divisionId) {
                    jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                    return;
                }
                
                $.ajax({
                    url: '/api/positions?division_id=' + divisionId,
                    success: function(response) {
                        jabatanSelect.html('<option value="">-- Pilih Jabatan --</option>');
                        if (response.data && response.data.length > 0) {
                            response.data.forEach(function(pos) {
                                jabatanSelect.append('<option value="' + pos.id + '">' + pos.name + '</option>');
                            });
                            // Set to initial value jika ada
                            var initialPosId = $('#editDivisiKaryawan').data('positionId');
                            if (initialPosId) {
                                jabatanSelect.val(initialPosId);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading positions:', xhr);
                    }
                });
            }

            // Handle Hapus Karyawan button click
            $(document).on('click', '.btn-hapus-karyawan', function() {
                var row = $(this).closest('tr');
                var nik = $(this).attr('data-nik');
                var nama = row.find('td').eq(3).text(); // Column 3: Nama

                // Show SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Hapus Karyawan?',
                    html: '<strong>' + nama + '</strong><br><small class="text-muted">NIK: ' + nik + '</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading dialog
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Tunggu sebentar, data sedang dihapus...',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Delete the employee
                        $.ajax({
                            url: '/api/employees/' + nik,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Karyawan ' + nama + ' berhasil dihapus.',
                                        icon: 'success',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        window.loadBothTables(); // Reload both tables
                                    });
                                }
                            },
                            error: function(xhr) {
                                var msg = 'Unknown error';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus karyawan: ' + msg,
                                    icon: 'error',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
            });

            // Handle tambah jabatan button
            $('#btnTambahJabatan').on('click', function() {
                var containerJabatan = $('#containerJabatan');
                var newItem = $(`
                    <div class="row mb-2 jabatan-item">
                        <div class="col-10">
                            <input type="text" class="form-control input-jabatan" placeholder="Masukkan nama jabatan" required>
                        </div>
                        <div class="col-2 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-jabatan">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `);
                containerJabatan.append(newItem);
                updateDeleteButtonVisibility();
            });

            // Handle hapus jabatan button (event delegation)
            $(document).on('click', '.btn-hapus-jabatan', function(e) {
                e.preventDefault();
                $(this).closest('.jabatan-item').remove();
                updateDeleteButtonVisibility();
            });

            // Function to show/hide delete buttons
            function updateDeleteButtonVisibility() {
                var count = $('#containerJabatan .jabatan-item').length;
                $('#containerJabatan .btn-hapus-jabatan').each(function() {
                    $(this).toggle(count > 1);
                });
            }

            // Initialize delete button visibility on page load
            updateDeleteButtonVisibility();

            // ===== HANDLERS UNTUK DEPARTEMEN SECTION SUDAH PINDAH KE GLOBAL FUNCTIONS ATAS =====

            // Function to load existing positions in edit mode
            // Positions are now grouped by divisions, so we load divisions first, then their positions
            function loadExistingPositions(departmentId) {
                // Get baseUrl dari current page location (lebih reliable)
                var baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '').replace(/\/public.*$/, '/public');
                var container = $('#editContainerJabatan');
                container.html('<p class="text-muted">Memuat jabatan...</p>');
                
                // Load all divisions for this department
                $.ajax({
                    url: baseUrl + '/api/divisions?department_id=' + departmentId,
                    success: function(divResponse) {
                        container.html('');
                        
                        if (!divResponse.data || divResponse.data.length === 0) {
                            container.html('<p class="text-muted">Belum ada divisi/jabatan untuk departemen ini</p>');
                            return;
                        }
                        
                        var allPositions = [];
                        var divisionsProcessed = 0;
                        
                        // For each division, load its positions
                        divResponse.data.forEach(function(division) {
                            $.ajax({
                                url: baseUrl + '/api/positions?division_id=' + division.id,
                                success: function(posResponse) {
                                    divisionsProcessed++;
                                    
                                    if (posResponse.data && posResponse.data.length > 0) {
                                        allPositions = allPositions.concat(posResponse.data);
                                    }
                                    
                                    // Once all divisions are processed, display positions
                                    if (divisionsProcessed === divResponse.data.length) {
                                        if (allPositions.length > 0) {
                                            allPositions.forEach(function(position) {
                                                var item = $(`
                                                    <div class="row mb-2 edit-jabatan-item" data-position-id="${position.id}">
                                                        <div class="col-10">
                                                            <input type="text" class="form-control input-edit-jabatan" value="${position.name}" readonly>
                                                        </div>
                                                        <div class="col-2 d-flex justify-content-end">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-jabatan-edit">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                `);
                                                container.append(item);
                                            });
                                        } else {
                                            container.html('<p class="text-muted">Belum ada jabatan untuk departemen ini</p>');
                                        }
                                    }
                                },
                                error: function(xhr) {
                                    divisionsProcessed++;
                                    console.error('Error loading positions for division:', xhr);
                                }
                            });
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading divisions:', xhr);
                        $('#editContainerJabatan').html('<p class="text-danger">Gagal memuat jabatan</p>');
                    }
                });
            }

            // --- ALL COMPLEX EDIT MODAL HANDLERS REMOVED ---
            // These were for old modal structure - now using simple onclick

            // ===== MODAL TAMBAH DEPARTEMEN EVENT HANDLERS =====
            // NOTE: Inisialisasi modal sekarang dilakukan di openTambahDeptModal()
            // Event handlers di sini hanya untuk logging dan cleanup tambahan jika diperlukan
            var modalTambahDept = document.getElementById('modalTambahDept');
            if (modalTambahDept) {
                console.log('✅ Setting up event listeners for modalTambahDept');
                
                // Listener saat modal sudah ditampilkan sepenuhnya (untuk logging saja)
                modalTambahDept.addEventListener('shown.bs.modal', function() {
                    console.log('🎬 Modal shown.bs.modal - modal is now fully visible');
                }, false);
                
                // Listener saat modal ditutup - untuk cleanup
                modalTambahDept.addEventListener('hidden.bs.modal', function() {
                    console.log('🔍 Modal hidden.bs.modal event triggered');
                    
                    // Force cleanup backdrop jika masih ada (safety net)
                    setTimeout(function() {
                        var backdrops = document.querySelectorAll('.modal-backdrop');
                        if (backdrops.length > 0) {
                            console.log('⚠️ Found lingering backdrop, cleaning up...');
                            backdrops.forEach(function(el) { el.remove(); });
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                        console.log('✅ Modal hidden cleanup done');
                    }, 100);
                }, false);
                
                console.log('✅ Modal event listeners registered');
            } else {
                console.error('❌ modalTambahDept element not found!');
            }
            
            // ===== MODAL EDIT DEPARTEMEN EVENT HANDLERS =====
            var modalEditDept = document.getElementById('modalEditDept');
            if (modalEditDept) {
                modalEditDept.addEventListener('hidden.bs.modal', function() {
                    // Reset form
                    var form = document.getElementById('formEditDept');
                    if (form) {
                        form.reset();
                    }
                    
                    // Clear containers
                    document.getElementById('containerDivisiEdit').innerHTML = '';
                    document.getElementById('containerJabatanEdit').innerHTML = '';
                    
                    console.log('✅ Modal edit form reset');
                });
            }

            // ===== MATIKAN beforeunload untuk auto reload =====
            window.onbeforeunload = null;
            window.removeEventListener('beforeunload', null);

            // Load initial data - AFTER all functions are defined
            console.log('🚀 Initializing page...');
            console.log('🔍 jQuery version:', $.fn.jquery);
            console.log('🔍 Current URL:', window.location.href);
            
            // Test if dropdown elements exist
            console.log('🔍 #departemenKaryawan exists?', $('#departemenKaryawan').length);
            console.log('🔍 #divisiKaryawan exists?', $('#divisiKaryawan').length);
            console.log('🔍 #jabatanKaryawan exists?', $('#jabatanKaryawan').length);
            console.log('🔍 Filter departemen element exists?', $('#filterDepartemenKaryawan').length);
            console.log('🔍 Filter divisi element exists?', $('#filterDivisiKaryawan').length);
            
            // Load both tables
            window.loadBothTables();
            
            console.log('✅ Initialization complete');

            // ===== TAMBAH KARYAWAN BARU: CASCADE DROPDOWN HANDLERS =====
            console.log('🔧 CASCADE: Initializing dropdown handlers...');
            console.log('   📦 Divisions available:', window.allDivisions ? window.allDivisions.length : 'NOT LOADED');
            console.log('   📦 Positions available:', window.allPositions ? window.allPositions.length : 'NOT LOADED');
            
            // Function to populate division dropdown
            function populateDivisions(deptId) {
                var $select = $('#divisionTambah');
                $select.empty().append('<option value="">-- Pilih Divisi --</option>');
                
                if (deptId && window.allDivisions) {
                    var count = 0;
                    window.allDivisions.forEach(function(div) {
                        if (div.department_id == deptId) {
                            $select.append('<option value="' + div.id + '">' + div.name + '</option>');
                            count++;
                        }
                    });
                    console.log('   → Added ' + count + ' divisions for dept ' + deptId);
                }
            }
            
            // Function to populate position dropdown
            function populatePositions(divId) {
                var $select = $('#positionTambah');
                $select.empty().append('<option value="">-- Pilih Jabatan --</option>');
                
                if (divId && window.allPositions) {
                    var count = 0;
                    window.allPositions.forEach(function(pos) {
                        if (pos.division_id == divId) {
                            $select.append('<option value="' + pos.id + '">' + pos.name + '</option>');
                            count++;
                        }
                    });
                    console.log('   → Added ' + count + ' positions for div ' + divId);
                }
            }
            
            // Reset modal saat dibuka
            $('#modalTambahKaryawan').on('shown.bs.modal', function() {
                console.log('🔄 CASCADE: Modal opened, resetting...');
                $('#departmentTambah').val('');
                populateDivisions(null);
                populatePositions(null);
            });
            
            // Department berubah -> populate Division
            $(document).on('change', '#departmentTambah', function() {
                const deptId = $(this).val();
                console.log('📦 CASCADE: Department changed to ' + deptId);
                
                populateDivisions(deptId);
                populatePositions(null);
            });
            
            // Division berubah -> populate Position
            $(document).on('change', '#divisionTambah', function() {
                const divId = $(this).val();
                console.log('📦 CASCADE: Division changed to ' + divId);
                
                populatePositions(divId);
            });
            
            console.log('✅ Cascade dropdown handlers ready (dynamic populate from JS array)');
            
            // NOTE: Handler simpan karyawan sudah dipindahkan ke inline script di dalam modal
            // Untuk menghindari duplikasi dan memastikan script ter-execute dengan benar

        }); // End document.ready

    </script>
@endpush

{{-- Include Department Modals --}}
@include('admin.departments.modals.edit-department')
@include('admin.departments.modals.detail-department')

{{-- Include Employee Modals --}}
@include('admin.employees.modals.edit-employee')


