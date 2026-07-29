@extends('layouts.app-user')

@section('title', 'Pelatihan Saya')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Daftar Ujian</h3>
                <p class="text-subtitle text-muted">Ujian yang belum atau sedang Anda kerjakan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pelatihan Saya</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <!-- Filter & Search -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Cari pelatihan..." value="{{ request('search') }}" onchange="window.autoSubmitForm(this.form)">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="window.autoSubmitForm(this.form)">
                            <option value="">Semua Status</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Belum Dimulai</option>
                            <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="level" class="form-select" onchange="window.autoSubmitForm(this.form)">
                            <option value="">Semua Level</option>
                            <option value="1" {{ request('level') == '1' ? 'selected' : '' }}>Level 1</option>
                            <option value="2" {{ request('level') == '2' ? 'selected' : '' }}>Level 2</option>
                            <option value="3" {{ request('level') == '3' ? 'selected' : '' }}>Level 3</option>
                            <option value="4" {{ request('level') == '4' ? 'selected' : '' }}>Level 4</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Training List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Ujian</th>
                            <th>Kategori</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Jadwal Mulai</th>
                            <th>Deadline</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $i => $session)
                            <tr>
                                <td>{{ $sessions->firstItem() + $i }}</td>
                                <td>
                                    <strong>{{ $session->exam->title }}</strong>
                                    @if($session->exam->description)
                                        <br><small class="text-muted">{{ Str::limit($session->exam->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $session->exam->skill->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Level {{ $session->exam->target_level }}</span>
                                </td>
                                <td>
                                    @if($session->status === 'assigned')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock"></i> Belum Dikerjakan
                                        </span>
                                    @elseif($session->status === 'started')
                                        <span class="badge bg-info">
                                            <i class="bi bi-play-circle"></i> Sedang Dikerjakan
                                        </span>
                                    @elseif($session->status === 'submitted')
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                        </span>
                                    @elseif($session->status === 'verified_pass')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Lulus ({{ $session->score }}%)
                                        </span>
                                    @elseif($session->status === 'verified_fail')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Tidak Lulus ({{ $session->score }}%)
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->scheduled_start_at)
                                        @if($session->isNotStartedYet())
                                            <span class="text-info fw-bold">{{ $session->scheduled_start_at->format('d M Y') }}</span>
                                            <br><small class="text-muted">{{ $session->scheduled_start_at->format('H:i') }} WIB</small>
                                        @else
                                            <span class="text-muted">{{ $session->scheduled_start_at->format('d M Y') }}</span>
                                            <br><small class="text-success"><i class="bi bi-check-circle"></i> Sudah dibuka</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->deadline_at)
                                        @php $deadlineStatus = $session->getDeadlineStatus(); @endphp
                                        <span class="{{ $deadlineStatus['class'] === 'danger' ? 'text-danger fw-bold' : ($deadlineStatus['class'] === 'warning' ? 'text-warning fw-bold' : '') }}">
                                            {{ $session->deadline_at->format('d M Y') }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $session->deadline_at->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->status === 'assigned')
                                        <a href="{{ route('cbt.employee.show', $session->id) }}" class="btn btn-sm btn-primary" title="Mulai Pelatihan">
                                            <i class="bi bi-play-circle"></i> Mulai
                                        </a>
                                    @elseif($session->status === 'started')
                                        <a href="{{ route('cbt.employee.take', $session->id) }}" class="btn btn-sm btn-warning" title="Lanjutkan Mengerjakan">
                                            <i class="bi bi-arrow-clockwise"></i> Lanjutkan
                                        </a>
                                    @elseif($session->status === 'submitted')
                                        <a href="{{ route('cbt.employee.result', $session->id) }}" class="btn btn-sm btn-secondary" title="Lihat Status Verifikasi">
                                            <i class="bi bi-hourglass-split"></i> Lihat Status
                                        </a>
                                    @else
                                        <a href="{{ route('cbt.employee.result', $session->id) }}" class="btn btn-sm btn-info" title="Lihat Hasil Ujian">
                                            <i class="bi bi-eye"></i> Lihat Hasil
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Belum ada pelatihan yang ditugaskan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sessions->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $sessions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
