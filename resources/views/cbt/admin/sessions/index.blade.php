@extends('layouts.app')

@section('title', 'Sesi Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Sesi Ujian</h3>
                <p class="text-subtitle text-muted">Penugasan dan hasil ujian karyawan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sesi Ujian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Daftar Sesi</h4>
            <a href="{{ route('cbt.admin.sessions.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Tugaskan Ujian
            </a>
        </div>
        <div class="card-body">
            {{-- Filters --}}
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                        <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>Dikerjakan</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Selesai (Pending)</option>
                        <option value="verified_pass" {{ request('status') == 'verified_pass' ? 'selected' : '' }}>Lulus - Menunggu Approval</option>
                        <option value="verified_fail" {{ request('status') == 'verified_fail' ? 'selected' : '' }}>Tidak Lulus</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui Manager</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak Manager</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('cbt.admin.sessions.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="18%">Karyawan</th>
                            <th width="18%">Ujian</th>
                            <th width="12%">Tanggal</th>
                            <th width="12%">Deadline</th>
                            <th width="8%">Nilai</th>
                            <th width="12%">Status</th>
                            <th width="8%">Verifikator</th>
                            <th width="7%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $i => $session)
                            <tr>
                                <td>{{ $sessions->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $session->employee->name ?? $session->employee_nik }}</strong>
                                    <br><small class="text-muted">{{ $session->employee->division->name ?? '-' }}</small>
                                </td>
                                <td>
                                    <strong>{{ $session->exam->title ?? '-' }}</strong>
                                    <br><small class="text-muted">{{ $session->exam->skill->name ?? '-' }}</small>
                                </td>
                                <td>
                                    {{ $session->created_at->format('d M Y') }}
                                    <br><small class="text-muted">{{ $session->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($session->deadline_at)
                                        @php
                                            $deadlineStatus = $session->getDeadlineStatus();
                                        @endphp
                                        {{ $session->deadline_at->format('d M Y') }}
                                        <br><small class="text-muted">{{ $session->deadline_at->format('H:i') }}</small>
                                        @if($deadlineStatus['label'])
                                            <br><span class="badge bg-{{ $deadlineStatus['class'] }} mt-1">
                                                <small>{{ $deadlineStatus['label'] }}</small>
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->score !== null)
                                        <span class="fw-bold {{ $session->score >= $session->exam->passing_score ? 'text-success' : 'text-danger' }}">
                                            {{ $session->score }}
                                        </span>
                                        <small class="text-muted">/ 100</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($session->status)
                                        @case('assigned')
                                            <span class="badge bg-secondary">Ditugaskan</span>
                                            @break
                                        @case('started')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split"></i> Dikerjakan
                                            </span>
                                            @break
                                        @case('submitted')
                                            <span class="badge bg-info">
                                                <i class="bi bi-clock"></i> Menunggu Verifikasi
                                            </span>
                                            @break
                                        @case('verified_pass')
                                            <span class="badge bg-primary">
                                                <i class="bi bi-hourglass"></i> Lulus - Menunggu Approval
                                            </span>
                                            @break
                                        @case('verified_fail')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> TIDAK LULUS
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Disetujui
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-dark">
                                                <i class="bi bi-x-circle"></i> Ditolak
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($session->verifier)
                                        {{ $session->verifier->name }}
                                        <br><small class="text-muted">{{ $session->verified_at?->format('d/m/y') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cbt.admin.sessions.show', $session) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($session->status === 'assigned')
                                        <form action="{{ route('cbt.admin.sessions.cancel', $session) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan penugasan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada sesi ujian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end">
                {{ $sessions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show detailed notification if there are not eligible or skipped employees
    @if(session('notEligibleList') || session('skippedList'))
        let html = '<div style="text-align: left;">';
        
        @if(session('assignedCount') && session('assignedCount') > 0)
            html += '<div class="alert alert-success mb-3"><i class="bi bi-check-circle"></i> <strong>{{ session("assignedCount") }} karyawan berhasil ditugaskan</strong></div>';
        @endif

        @if(session('notEligibleList') && count(session('notEligibleList')) > 0)
            html += '<div class="mb-3"><h6 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Tidak Memenuhi Syarat Level:</h6>';
            html += '<ul style="margin-bottom: 0;">';
            @foreach(session('notEligibleList') as $emp)
                html += '<li><strong>{{ $emp['name'] }}</strong> ({{ $emp['nik'] }}) - Level saat ini: <span class="badge bg-warning">{{ $emp['current_level'] }}</span>, Required: <span class="badge bg-info">{{ $emp['required_level'] }}</span>, Target: <span class="badge bg-success">{{ $emp['target_level'] }}</span></li>';
            @endforeach
            html += '</ul></div>';
        @endif

        @if(session('skippedList') && count(session('skippedList')) > 0)
            html += '<div class="mb-3"><h6 class="text-warning"><i class="bi bi-info-circle"></i> Dilewati:</h6>';
            html += '<ul style="margin-bottom: 0;">';
            @foreach(session('skippedList') as $emp)
                html += '<li><strong>{{ $emp['name'] }}</strong> ({{ $emp['nik'] }}) - {{ $emp['reason'] }}</li>';
            @endforeach
            html += '</ul></div>';
        @endif

        html += '</div>';

        Swal.fire({
            title: 'Detail Penugasan Ujian',
            html: html,
            icon: 'info',
            confirmButtonText: 'OK',
            width: '600px',
            customClass: {
                popup: 'swal-wide'
            }
        });
    @endif
</script>
@endpush
