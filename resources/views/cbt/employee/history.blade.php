@extends('layouts.app')

@section('title', 'Riwayat Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Riwayat Ujian</h3>
                <p class="text-subtitle text-muted">Semua ujian yang pernah Anda ikuti</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('cbt.employee.dashboard') }}">CBT</a></li>
                        <li class="breadcrumb-item active">Riwayat Ujian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="card-title mb-0">Daftar Ujian</h5>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex gap-2 justify-content-end">
                        <select name="status" class="form-select w-auto">
                            <option value="">Semua Status</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                            <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="verified_pass" {{ request('status') == 'verified_pass' ? 'selected' : '' }}>Lulus - Menunggu Approval</option>
                            <option value="verified_fail" {{ request('status') == 'verified_fail' ? 'selected' : '' }}>Tidak Lulus</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($sessions->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Belum Ada Riwayat</h4>
                    <p class="text-muted">Anda belum pernah mengikuti ujian apapun.</p>
                    <a href="{{ route('cbt.employee.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-play"></i> Lihat Ujian Tersedia
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ujian</th>
                                <th>Skill</th>
                                <th>Target Level</th>
                                <th>Status</th>
                                <th>Nilai</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                @php
                                    $statusColors = [
                                        'assigned' => 'secondary',
                                        'started' => 'warning',
                                        'submitted' => 'info',
                                        'verified_pass' => 'primary',
                                        'verified_fail' => 'danger',
                                        'approved' => 'success',
                                        'rejected' => 'dark',
                                    ];
                                    $statusLabels = [
                                        'assigned' => 'Ditugaskan',
                                        'started' => 'Sedang Dikerjakan',
                                        'submitted' => 'Menunggu Verifikasi',
                                        'verified_pass' => 'Lulus - Menunggu Approval',
                                        'verified_fail' => 'Tidak Lulus',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $session->exam->title }}</strong>
                                    </td>
                                    <td>{{ $session->exam->skill->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">Level {{ $session->exam->target_level }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusColors[$session->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$session->status] ?? $session->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($session->status, ['submitted', 'verified_pass', 'verified_fail', 'approved', 'rejected']))
                                            <span class="{{ $session->score >= $session->exam->passing_score ? 'text-success' : 'text-danger' }} fw-bold">
                                                {{ $session->score }} / 100
                                            </span>
                                            <br>
                                            <small class="text-muted">KKM: {{ $session->exam->passing_score }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->submitted_at)
                                            {{ $session->submitted_at?->format('d M Y') ?? '-' }}
                                            <br>
                                            <small class="text-muted">{{ $session->submitted_at?->format('H:i') ?? '' }}</small>
                                        @elseif($session->started_at)
                                            {{ $session->started_at?->format('d M Y') ?? '-' }}
                                            <br>
                                            <small class="text-muted">{{ $session->started_at?->format('H:i') ?? '' }}</small>
                                        @else
                                            {{ $session->created_at?->format('d M Y') ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->status === 'assigned')
                                            <a href="{{ route('cbt.employee.show', $session) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-play"></i> Mulai
                                            </a>
                                        @elseif($session->status === 'started')
                                            <a href="{{ route('cbt.employee.take', $session) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-arrow-right"></i> Lanjutkan
                                            </a>
                                        @else
                                            <a href="{{ route('cbt.employee.result', $session) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Lihat Hasil
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Statistics --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-primary">{{ $stats['total'] ?? 0 }}</h2>
                    <small class="text-muted">Total Ujian</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-success">{{ $stats['passed'] ?? 0 }}</h2>
                    <small class="text-muted">Lulus</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-danger">{{ $stats['failed'] ?? 0 }}</h2>
                    <small class="text-muted">Tidak Lulus</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="text-info">{{ $stats['pending'] ?? 0 }}</h2>
                    <small class="text-muted">Menunggu Verifikasi</small>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
