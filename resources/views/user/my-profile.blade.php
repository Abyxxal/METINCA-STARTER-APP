@extends('layouts.app-user')

@section('title', 'Profil Saya')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Profil Saya</h3>
                <p class="text-subtitle text-muted">Kelola informasi profil dan akun Anda</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" 
                                     class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light-primary d-inline-flex align-items-center justify-content-center" 
                                     style="width: 120px; height: 120px;">
                                    <i class="bi bi-person fs-1 text-primary"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                        <p class="text-muted mb-3">{{ Auth::user()->employee->position->name ?? '-' }}</p>
                        
                        <div class="list-group list-group-flush text-start">
                            <div class="list-group-item border-0 px-0">
                                <small class="text-muted d-block">Email</small>
                                <p class="mb-0 fw-semibold">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="list-group-item border-0 px-0">
                                <small class="text-muted d-block">NIK</small>
                                <p class="mb-0 fw-semibold">{{ Auth::user()->employee->nik ?? '-' }}</p>
                            </div>
                            <div class="list-group-item border-0 px-0">
                                <small class="text-muted d-block">Departemen</small>
                                <p class="mb-0 fw-semibold">{{ Auth::user()->employee->department->name ?? '-' }}</p>
                            </div>
                            <div class="list-group-item border-0 px-0">
                                <small class="text-muted d-block">Divisi</small>
                                <p class="mb-0 fw-semibold">{{ Auth::user()->employee->division->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Profile Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">NIK</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ Auth::user()->employee->nik ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Departemen</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ Auth::user()->employee->department->name ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Divisi</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ Auth::user()->employee->division->name ?? '-' }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Jabatan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ Auth::user()->employee->position->name ?? '-' }}" disabled>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Ubah Password</h6>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Password Lama</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" placeholder="Masukkan password lama">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Password Baru</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" placeholder="Masukkan password baru">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Konfirmasi Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" placeholder="Konfirmasi password baru">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="button" class="btn btn-primary" onclick="saveProfile()">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                                <button type="reset" class="btn btn-secondary ms-2">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Section -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-success me-3">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Menyelesaikan Pelatihan</h6>
                                    <p class="text-muted mb-0 small">Machine Operation - Advanced (92%) - 15 Nov 2025</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-info me-3">
                                    <i class="fas fa-book text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Memulai Pelatihan</h6>
                                    <p class="text-muted mb-0 small">Quality Control Basics - 20 Oct 2025</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-warning me-3">
                                    <i class="fas fa-sign-in-alt text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Login Pertama</h6>
                                    <p class="text-muted mb-0 small">Akun dibuat - 01 Aug 2025</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ganti Foto -->
<div class="modal fade" id="editPhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ganti Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="photoInput" class="form-label">Pilih Foto</label>
                    <input type="file" class="form-control" id="photoInput" accept="image/*">
                </div>
                <div class="text-center">
                    <img id="photoPreview" src="{{ Auth::user()->profile_photo_url }}" alt="Preview" style="max-width: 200px; max-height: 200px;" class="rounded">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Photo preview
    document.getElementById('photoInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    function saveProfile() {
        Swal.fire({
            title: 'Sukses!',
            text: 'Profil Anda telah diperbarui',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }
</script>
@endsection
