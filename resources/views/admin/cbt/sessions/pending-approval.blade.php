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
            <h4 class="card-title">Menunggu Persetujuan ({{ $sessions->total() }})</h4>
        </div>
        <div class="card-body">
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
                                <td>
                                    <span class="fw-bold text-success">{{ $session->score }}</span>
                                    <small class="text-muted">/ 100</small>
                                </td>
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
                                    <div class="d-flex gap-1">
                                        <!-- Detail -->
                                        <a href="{{ route('cbt.admin.sessions.show', $session) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <!-- Approve -->
                                        <button type="button" class="btn btn-sm btn-success" 
                                            data-bs-toggle="modal" data-bs-target="#approveModal{{ $session->id }}" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <!-- Reject -->
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $session->id }}" title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $session->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('cbt.admin.sessions.approve-level', $session) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title"><i class="bi bi-check-circle"></i> Setujui Kenaikan Level</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info">
                                                            <strong>{{ $session->employee->name }}</strong> lulus ujian
                                                            <strong>{{ $session->exam->title }}</strong> dengan nilai <strong>{{ $session->score }}%</strong>.
                                                            <br>Skill <strong>{{ $session->exam->skill->name ?? '-' }}</strong> akan dinaikkan ke <strong>Level {{ $session->exam->target_level }}</strong>.
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Catatan (Opsional)</label>
                                                            <textarea name="manager_notes" class="form-control" rows="3" placeholder="Catatan manager..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Setujui Kenaikan Level</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $session->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('cbt.admin.sessions.reject-level', $session) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="bi bi-x-circle"></i> Tolak Kenaikan Level</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-warning">
                                                            <strong>{{ $session->employee->name }}</strong> lulus ujian
                                                            <strong>{{ $session->exam->title }}</strong> dengan nilai <strong>{{ $session->score }}%</strong>,
                                                            namun level <strong>TIDAK</strong> akan dinaikkan.
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea name="manager_notes" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger"><i class="bi bi-x-lg"></i> Tolak Kenaikan Level</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
        </div>
    </div>
</section>
@endsection
