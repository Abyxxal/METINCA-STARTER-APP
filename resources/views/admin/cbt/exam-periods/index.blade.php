@extends('layouts.app')

@section('title', 'Periode Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Periode Ujian</h3>
                <p class="text-subtitle text-muted">Jadwal &amp; rentang ujian yang ditetapkan Manager sebagai acuan penugasan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Periode Ujian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bi bi-calendar-event me-1"></i> Daftar Periode Ujian</h5>
            @if(Auth::user()->isManager())
                <a href="{{ route('cbt.admin.exam-periods.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Periode
                </a>
            @endif
        </div>
        <div class="card-body">
            @if($periods->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                    <p>Belum ada periode ujian ditetapkan.</p>
                    @if(Auth::user()->isManager())
                        <a href="{{ route('cbt.admin.exam-periods.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Tentukan Periode Pertama
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Periode</th>
                                <th>Lingkup</th>
                                <th>Mulai</th>
                                <th>Berakhir</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                @if(Auth::user()->isManager())
                                    <th class="text-center" width="110">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periods as $index => $period)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $period->name }}</strong>
                                        @if($period->creator)
                                            <br><small class="text-muted">ditetapkan {{ $period->creator->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($period->division)
                                            <span class="badge bg-light-primary">
                                                @if($period->division->department)
                                                    {{ $period->division->department->name }} →
                                                @endif
                                                {{ $period->division->name }}
                                            </span>
                                        @elseif($period->department)
                                            <span class="badge bg-info">{{ $period->department->name }} (Semua Divisi)</span>
                                        @else
                                            <span class="badge bg-secondary">Semua</span>
                                        @endif
                                    </td>
                                    <td>{{ $period->start_at ? $period->start_at->format('d M Y H:i') : '-' }}</td>
                                    <td>{{ $period->end_at ? $period->end_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        @if($period->isActive())
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                                        @elseif($period->end_at && now()->gt($period->end_at))
                                            <span class="badge bg-secondary"><i class="bi bi-clock-history me-1"></i>Selesai</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Akan Datang</span>
                                        @endif
                                    </td>
                                    <td>{{ $period->notes ?: '-' }}</td>
                                    @if(Auth::user()->isManager())
                                        <td class="text-center">
                                            <a href="{{ route('cbt.admin.exam-periods.edit', $period) }}"
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('cbt.admin.exam-periods.destroy', $period) }}" method="POST"
                                                  class="d-inline form-delete-period">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.form-delete-period').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const f = this;
            Swal.fire({
                title: 'Yakin ingin menghapus periode ini?',
                text: 'Periode yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    f.submit();
                }
            });
        });
    });
});
</script>
@endpush
@endsection