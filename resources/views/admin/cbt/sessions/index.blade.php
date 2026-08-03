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
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'berlangsung' ? 'active' : '' }}"
                       href="{{ route('cbt.admin.sessions.index', ['tab' => 'berlangsung']) }}" role="tab">
                        <i class="bi bi-hourglass-split"></i> Ujian Berlangsung
                        @if($ongoingCount > 0)
                            <span class="badge bg-primary">{{ $ongoingCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'selesai' ? 'active' : '' }}"
                       href="{{ route('cbt.admin.sessions.index', ['tab' => 'selesai']) }}" role="tab">
                        <i class="bi bi-clock-history"></i> Ujian Selesai
                        @if($completedCount > 0)
                            <span class="badge bg-secondary">{{ $completedCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
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
                        @if($tab === 'berlangsung')
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                            <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>Dikerjakan</option>
                        @else
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Selesai (Pending)</option>
                            <option value="verified_pass" {{ request('status') == 'verified_pass' ? 'selected' : '' }}>Lulus - Menunggu Approval</option>
                            <option value="verified_fail" {{ request('status') == 'verified_fail' ? 'selected' : '' }}>Tidak Lulus</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui Manager</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak Manager</option>
                        @endif
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
                            <th width="4%">#</th>
                            <th width="15%">Karyawan</th>
                            <th width="15%">Ujian</th>
                            <th width="10%">Tgl Ditugaskan</th>
                            <th width="10%">Tgl Mulai</th>
                            <th width="10%">Deadline</th>
                            <th width="7%">Nilai</th>
                            <th width="11%">Status</th>
                            <th width="8%">Verifikator</th>
                            <th width="10%">Aksi</th>
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
                                    @if($session->scheduled_start_at)
                                        <span class="text-info fw-semibold">{{ $session->scheduled_start_at->format('d M Y') }}</span>
                                        <br><small class="text-muted">{{ $session->scheduled_start_at->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
                                    @if(in_array($session->status, ['assigned', 'started']))
                                        <button type="button" class="btn btn-sm btn-outline-warning btn-edit-session" title="Edit Pengaturan"
                                            data-id="{{ $session->id }}"
                                            data-employee="{{ $session->employee->name ?? $session->employee_nik }}"
                                            data-exam="{{ $session->exam->title ?? '-' }}"
                                            data-deadline="{{ $session->deadline_at?->format('Y-m-d\TH:i') }}"
                                            data-scheduled="{{ $session->scheduled_start_at?->format('Y-m-d\TH:i') }}"
                                            data-passing-score="{{ $session->exam->passing_score ?? 70 }}"
                                            data-duration="{{ $session->exam->duration_minutes ?? 60 }}"
                                            data-update-url="{{ route('cbt.admin.sessions.update', $session) }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endif
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
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    @if($tab === 'berlangsung')
                                        Tidak ada ujian berlangsung.
                                    @else
                                        Belum ada riwayat ujian selesai.
                                    @endif
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

{{-- Modal Edit Sesi --}}
<div class="modal fade" id="editSessionModal" tabindex="-1" aria-labelledby="editSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSessionModalLabel">
                    <i class="bi bi-pencil-square"></i> Edit Pengaturan Sesi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditSession" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <p class="mb-1"><strong>Karyawan:</strong> <span id="edit-employee-name"></span></p>
                        <p class="mb-0"><strong>Ujian:</strong> <span id="edit-exam-title"></span></p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">KKM / Nilai Minimum Lulus <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="passing_score" id="edit-passing-score"
                                    min="0" max="100" required>
                                <span class="input-group-text">/ 100</span>
                            </div>
                            <small class="text-muted">Nilai minimum untuk lulus ujian ini.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Waktu Pengerjaan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="duration_minutes" id="edit-duration"
                                    min="5" max="300" required>
                                <span class="input-group-text">menit</span>
                            </div>
                            <small class="text-muted">Durasi ujian dalam menit.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jadwal Mulai</label>
                        <input type="datetime-local" class="form-control" name="scheduled_start_at" id="edit-scheduled">
                        <small class="text-muted">Opsional. Harus lebih awal dari deadline.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tenggat Waktu (Deadline) <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="deadline_at" id="edit-deadline" required>
                    </div>

                    <div class="alert alert-warning py-2 mb-0">
                        <i class="bi bi-info-circle"></i>
                        <small>Perubahan KKM dan waktu pengerjaan akan mempengaruhi semua karyawan yang ditugaskan pada ujian yang sama.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Edit Session Modal
    document.querySelectorAll('.btn-edit-session').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var deadline  = this.dataset.deadline  || '';
            var scheduled = this.dataset.scheduled || '';

            document.getElementById('edit-employee-name').textContent = this.dataset.employee;
            document.getElementById('edit-exam-title').textContent    = this.dataset.exam;
            document.getElementById('edit-passing-score').value       = this.dataset.passingScore;
            document.getElementById('edit-duration').value            = this.dataset.duration;
            document.getElementById('formEditSession').action         = this.dataset.updateUrl;

            // Set datetime-local values after a tiny delay so modal is fully rendered
            var deadlineInput   = document.getElementById('edit-deadline');
            var scheduledInput  = document.getElementById('edit-scheduled');
            deadlineInput.value  = '';
            scheduledInput.value = '';
            setTimeout(function() {
                deadlineInput.value  = deadline;
                scheduledInput.value = scheduled;
            }, 50);

            var modal = new bootstrap.Modal(document.getElementById('editSessionModal'));
            modal.show();
        });
    });

    // Flash messages
    @if(session('success'))
        App.toast('success', '{{ addslashes(session('success')) }}');
    @endif
    @if(session('error'))
        App.toast('error', '{{ addslashes(session('error')) }}');
    @endif

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
