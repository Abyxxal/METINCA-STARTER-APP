@extends('layouts.app')

@section('title', 'Import Data Karyawan')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Import Data Karyawan</h3>
                <p class="text-subtitle text-muted">Import data karyawan dari file Excel</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('master-data') }}">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Import Karyawan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Import Gagal!</strong>
            @if ($errors->has('file'))
                <div>{{ $errors->first('file') }}</div>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        @if (session('failures'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Errors ditemukan di file:</strong>
                <div class="table-responsive">
<table class="table table-sm mt-2 mb-0">
                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>Attribute</th>
                            <th>Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('failures') as $failure)
                            <tr>
                                <td>{{ $failure->row() }}</td>
                                <td>{{ $failure->attribute() }}</td>
                                <td>{{ implode(', ', $failure->errors()) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                
</div></table>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Upload File Excel</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <div class="input-group">
                                <input type="file" class="form-control @error('file') is-invalid @enderror"
                                    id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                <button class="btn btn-outline-secondary" type="button" id="downloadTemplate">
                                    <i class="bi bi-download me-2"></i>Download Template
                                </button>
                            </div>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: .xlsx, .xls, atau .csv</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="skipDuplicate" name="skip_duplicate">
                                <label class="form-check-label" for="skipDuplicate">
                                    Lewati duplikat NIK (jika ada)
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-upload me-2"></i>Import Data
                        </button>
                        <a href="{{ route('master-data') }}" class="btn btn-secondary btn-lg ms-2">
                            <i class="bi bi-x-lg me-2"></i>Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Panduan Import</h5>
                </div>
                <div class="card-body">
                    <h6>Kolom yang Diperlukan:</h6>
                    <ul class="small">
                        <li><strong>nik</strong> - Nomor Induk Karyawan (unique)</li>
                        <li><strong>name</strong> - Nama Lengkap</li>
                        <li><strong>email</strong> - Email (optional, unique)</li>
                        <li><strong>department_id</strong> - ID Departemen</li>
                        <li><strong>position_id</strong> - ID Jabatan</li>
                        <li><strong>phone</strong> - Nomor Telepon (optional)</li>
                        <li><strong>address</strong> - Alamat (optional)</li>
                        <li><strong>photo_path</strong> - Path Foto (optional)</li>
                        <li><strong>hire_date</strong> - Tanggal Masuk (format: YYYY-MM-DD)</li>
                        <li><strong>status</strong> - Status (active/inactive/resigned)</li>
                    </ul>

                    <hr>

                    <h6>Ketentuan:</h6>
                    <ul class="small">
                        <li>Baris pertama harus berisi header</li>
                        <li>NIK harus unik (tidak duplikat)</li>
                        <li>Email harus unik jika diisi</li>
                        <li>Department & Position ID harus valid</li>
                        <li>Status: active, inactive, atau resigned</li>
                    </ul>

                    <hr>

                    <a href="{{ route('employee.template') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-file-earmark-excel me-2"></i>Download Template
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('downloadTemplate').addEventListener('click', function() {
        window.location.href = "{{ route('employee.template') }}";
    });
</script>
@endpush
