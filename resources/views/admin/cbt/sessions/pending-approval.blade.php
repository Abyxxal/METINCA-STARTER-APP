@extends('layouts.app')

@section('title', 'Persetujuan Kenaikan Level')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Persetujuan Kenaikan Level</h3>
                <p class="text-subtitle text-muted">Daftar karyawan yang lulus ujian dan menunggu persetujuan kenaikan level</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Persetujuan Level</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" 
                       href="{{ route('cbt.admin.sessions.pending-approval', ['tab' => 'pending']) }}" 
                       role="tab">
                       <i class="bi bi-clock"></i> Menunggu Persetujuan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}" 
                       href="{{ route('cbt.admin.sessions.pending-approval', ['tab' => 'history']) }}" 
                       role="tab">
                       <i class="bi bi-clock-history"></i> Riwayat Approval
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @if($tab === 'history')
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Karyawan</th>
                                <th>Kompetensi</th>
                                <th>Level Diajukan</th>
                                <th>Nilai</th>
                                <th>Status Threshold</th>
                                <th>Hasil Kualitatif</th>
                                <th>Keputusan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $i => $session)
                                @php
                                    $assessment = $session->managerAssessment;
                                    $overallResult = $assessment?->getOverallResult();
                                @endphp
                                <tr>
                                    <td>{{ $sessions->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $session->employee->name ?? $session->employee_nik }}</strong>
                                        <br><small class="text-muted">{{ $session->employee->division->name ?? '-' }} &bull; {{ $session->employee->position->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $session->exam->skill->name ?? '-' }}</span>
                                    </td>
                                    <td><span class="badge bg-secondary">Level {{ $session->exam->target_level }}</span></td>
                                    <td class="fw-bold">{{ $session->score }}</td>
                                    <td>
                                        @if($session->score >= $session->exam->passing_score)
                                            <span class="badge bg-success">Memenuhi</span>
                                        @else
                                            <span class="badge bg-danger">Tidak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($overallResult && $overallResult === 'memenuhi')
                                            <span class="badge bg-success">Memenuhi</span>
                                        @elseif($overallResult && $overallResult === 'perlu_perbaikan')
                                            <span class="badge bg-warning">Perlu Perbaikan</span>
                                        @elseif($overallResult && $overallResult === 'tidak_memenuhi')
                                            <span class="badge bg-danger">Tidak Memenuhi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->manager_decision === 'approved')
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Disetujui</span>
                                        @elseif($session->manager_decision === 'rejected')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $session->decided_at?->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" data-bs-target="#detailModal{{ $session->id }}" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                        Belum ada riwayat approval.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $sessions->links() }}
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Karyawan</th>
                                <th>Ujian</th>
                                <th>Skill & Target Level</th>
                                <th>Nilai</th>
                                <th>KKM</th>
                                <th>Diverifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $i => $session)
                                <tr>
                                    <td>{{ $sessions->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $session->employee->name ?? $session->employee_nik }}</strong>
                                        <br><small class="text-muted">{{ $session->employee->division->name ?? '-' }} &bull; {{ $session->employee->position->name ?? '-' }}</small>
                                    </td>
                                    <td>{{ $session->exam->title }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $session->exam->skill->name ?? '-' }}</span>
                                        <span class="badge bg-secondary">Level {{ $session->exam->target_level }}</span>
                                    </td>
                                    <td class="fw-bold text-success">{{ $session->score }}</td>
                                    <td>{{ $session->exam->passing_score }}</td>
                                    <td>
                                        @if($session->verifier)
                                            {{ $session->verifier->name }}
                                            <br><small class="text-muted">{{ $session->verified_at?->format('d/m/Y H:i') }}</small>
                                        @else
                                            <small class="text-muted">Auto-verified</small>
                                            <br><small class="text-muted">{{ $session->verified_at?->format('d/m/Y H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('cbt.admin.sessions.assessment', $session) }}" class="btn btn-sm btn-outline-info" title="Penilaian Kualitatif">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                                        Tidak ada yang menunggu persetujuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $sessions->links() }}
            @endif
        </div>
    </div>
</section>

@if($tab === 'history')
@foreach($sessions as $session)
    @php $assessment = $session->managerAssessment; @endphp
    <div class="modal fade" id="detailModal{{ $session->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detail Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <td class="text-muted" width="30%">Karyawan</td>
                            <td><strong>{{ $session->employee->name ?? $session->employee_nik }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kompetensi</td>
                            <td>{{ $session->exam->skill->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Level Diajukan</td>
                            <td>Level {{ $session->exam->target_level }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nilai Kuantitatif</td>
                            <td class="fw-bold">{{ $session->score }} / 100 (KKM: {{ $session->exam->passing_score }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Threshold</td>
                            <td>
                                @if($session->score >= $session->exam->passing_score)
                                    <span class="badge bg-success">Memenuhi</span>
                                @else
                                    <span class="badge bg-danger">Tidak Memenuhi</span>
                                @endif
                            </td>
                        </tr>
                        @if($assessment)
                        <tr>
                            <td class="text-muted">Hasil Kualitatif</td>
                            <td>
                                @php
                                    $or = $assessment->getOverallResult();
                                    $rl = ['memenuhi' => 'success', 'perlu_perbaikan' => 'warning', 'tidak_memenuhi' => 'danger'];
                                @endphp
                                @if(isset($rl[$or]))
                                    <span class="badge bg-{{ $rl[$or] }}">{{ $assessment::$criteriaStatusLabels[$or] }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode Verifikasi</td>
                            <td>{{ $assessment::$methodLabels[$assessment->assessment_method] ?? '-' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Keputusan Manager</td>
                            <td>
                                @if($session->manager_decision === 'approved')
                                    <span class="badge bg-success fs-6">DISETUJUI</span>
                                @else
                                    <span class="badge bg-danger fs-6">DITOLAK</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Diputuskan oleh</td>
                            <td>{{ $session->manager->name ?? '-' }} &bull; {{ $session->decided_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($session->manager_notes)
                        <tr>
                            <td class="text-muted">Catatan Manager</td>
                            <td class="fst-italic">"{{ $session->manager_notes }}"</td>
                        </tr>
                        @endif
                    </table>

                    @if($assessment)
                    <h6 class="mt-3">Detail Penilaian Kualitatif</h6>
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Kriteria</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessment::$criteriaLabels as $field => $label)
                                @if($assessment->$field)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td>
                                        <span class="badge {{ $assessment::$criteriaBadgeClasses[$assessment->$field] }}">
                                            {{ $assessment::$criteriaStatusLabels[$assessment->$field] }}
                                        </span>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endif
@endsection