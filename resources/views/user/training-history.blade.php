@extends('layouts.app-user')

@section('title', 'Riwayat Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Riwayat Ujian</h3>
                <p class="text-subtitle text-muted">Daftar ujian yang telah Anda selesaikan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Riwayat Ujian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <section class="section">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('user.training-history') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Cari pelatihan..." 
                                   value="{{ request('search') }}" onchange="window.autoSubmitForm(this.form)">
                        </div>
                        <div class="col-md-3">
                            <select name="year" class="form-select" onchange="window.autoSubmitForm(this.form)">
                                <option value="">Semua Tahun</option>
                                <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2025" {{ request('year') == '2025' ? 'selected' : '' }}>2025</option>
                                <option value="2024" {{ request('year') == '2024' ? 'selected' : '' }}>2024</option>
                                <option value="2023" {{ request('year') == '2023' ? 'selected' : '' }}>2023</option>
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
    </section>

    <!-- Training History List -->
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Ujian</th>
                                <th>Kategori</th>
                                <th>Level</th>
                                <th>Tanggal Selesai</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $index => $session)
                            <tr>
                                <td>{{ $sessions->firstItem() + $index }}</td>
                                <td><strong>{{ $session->exam->title }}</strong></td>
                                <td>
                                    <span class="badge bg-light-info">{{ $session->exam->skill->code }}</span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($session->exam->target_level == 1) bg-light-success
                                        @elseif($session->exam->target_level == 2) bg-light-warning
                                        @elseif($session->exam->target_level == 3) bg-light-danger
                                        @else bg-light-dark
                                        @endif">
                                        Level {{ $session->exam->target_level }}
                                    </span>
                                </td>
                                <td>
                                    @if($session->verified_at)
                                        {{ $session->verified_at->format('d M Y') }}
                                    @elseif($session->submitted_at)
                                        {{ $session->submitted_at->format('d M Y') }}
                                        <br><small class="text-muted">(Dikumpulkan)</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($session->status === 'submitted')
                                        <div class="text-muted">
                                            <i class="bi bi-hourglass-split"></i> Menunggu
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold me-2">{{ $session->score ?? 0 }}</span>
                                            <div class="progress" style="width: 80px; height: 6px;">
                                                <div class="progress-bar 
                                                    @if(($session->score ?? 0) >= $session->exam->passing_score) bg-success 
                                                    @else bg-danger 
                                                    @endif" 
                                                    style="width: {{ min(($session->score ?? 0), 100) }}%;"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($session->status === 'submitted')
                                        <span class="badge bg-light-secondary">
                                            <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                                        </span>
                                    @elseif($session->status === 'verified_pass')
                                        <span class="badge bg-light-warning">
                                            <i class="bi bi-clock"></i> Lulus - Menunggu Persetujuan
                                        </span>
                                    @elseif($session->status === 'approved')
                                        <span class="badge bg-light-success">
                                            <i class="bi bi-check-circle"></i> Lulus - Disetujui
                                        </span>
                                    @elseif($session->status === 'rejected')
                                        <span class="badge bg-light-danger">
                                            <i class="bi bi-x-circle"></i> Lulus - Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-light-danger">
                                            <i class="bi bi-x-circle"></i> Tidak Lulus
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cbt.employee.result', $session->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       data-bs-toggle="tooltip" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada riwayat pelatihan
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($sessions->hasPages())
                <div class="mt-4">
                    {{ $sessions->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
