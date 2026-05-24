@extends('layouts.app')

@section('title', 'Penilaian Hasil Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Penilaian Hasil Ujian</h3>
                <p class="text-subtitle text-muted">Ujian yang menunggu verifikasi</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cbt.admin.sessions.index') }}">Sesi Ujian</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Verifikasi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="bi bi-clock-history text-warning"></i>
                Menunggu Verifikasi ({{ $sessions->total() }})
            </h4>
        </div>
        <div class="card-body">
            @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Karyawan</th>
                                <th width="25%">Ujian</th>
                                <th width="15%">Selesai</th>
                                <th width="10%">Nilai</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $i => $session)
                                <tr>
                                    <td>{{ $sessions->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $session->employee->name ?? $session->employee_nik }}</strong>
                                        <br><small class="text-muted">{{ $session->employee->division->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        {{ $session->exam->title }}
                                        <br><small class="text-muted">{{ $session->exam->skill->name ?? '-' }} - Level {{ $session->exam->target_level }}</small>
                                    </td>
                                    <td>
                                        {{ $session->finished_at?->format('d M Y') }}
                                        <br><small class="text-muted">{{ $session->finished_at?->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold fs-5 {{ $session->score >= $session->exam->passing_score ? 'text-success' : 'text-danger' }}">
                                            {{ $session->score }}
                                        </span>
                                        <br><small class="text-muted">KKM: {{ $session->exam->passing_score }}</small>
                                    </td>
                                    <td>
                                        @if($session->score >= $session->exam->passing_score)
                                            <span class="badge bg-success">LULUS</span>
                                        @else
                                            <span class="badge bg-danger">TIDAK LULUS</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('cbt.admin.sessions.show', $session) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Review & Verifikasi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-check-circle text-success display-1"></i>
                    <h4 class="mt-3 text-muted">Tidak ada ujian yang menunggu verifikasi</h4>
                    <a href="{{ route('cbt.admin.sessions.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-arrow-left"></i> Lihat Semua Sesi
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
