@extends('layouts.app-user')

@section('title', 'Hasil Ujian')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Hasil Ujian</h3>
                <p class="text-subtitle text-muted">{{ $session->exam->title ?? 'Ujian' }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.my-training') }}">Pelatihan Saya</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Hasil Ujian</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Result Card --}}
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    @if($session->status === 'approved')
                        <div class="mb-4">
                            <i class="bi bi-trophy-fill display-1 text-success"></i>
                        </div>
                        <h2 class="text-success mb-3">
                            <i class="bi bi-check-circle-fill"></i> LULUS & DISETUJUI
                        </h2>
                        <p class="text-muted">Selamat! Anda telah lulus ujian ini dan kenaikan level telah disetujui oleh Manager.</p>
                        @if($session->manager_notes)
                            <div class="alert alert-success mt-3">
                                <i class="bi bi-chat-left-text"></i> <strong>Catatan Manager:</strong> {{ $session->manager_notes }}
                            </div>
                        @endif
                    @elseif($session->status === 'verified_pass')
                        <div class="mb-4">
                            <i class="bi bi-hourglass-split display-1 text-primary"></i>
                        </div>
                        <h2 class="text-primary mb-3">
                            <i class="bi bi-hourglass-split"></i> LULUS - MENUNGGU PERSETUJUAN
                        </h2>
                        <p class="text-muted mb-2">Selamat! Anda telah lulus ujian ini. Kenaikan level sedang menunggu persetujuan Manager.</p>
                    @elseif($session->status === 'rejected')
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill display-1 text-dark"></i>
                        </div>
                        <h2 class="text-dark mb-3">
                            <i class="bi bi-x-circle-fill"></i> LULUS - KENAIKAN DITOLAK
                        </h2>
                        <p class="text-muted">Anda lulus ujian ini, namun kenaikan level ditolak oleh Manager.</p>
                        @if($session->manager_notes)
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-chat-left-text"></i> <strong>Catatan Manager:</strong> {{ $session->manager_notes }}
                            </div>
                        @endif
                    @elseif($session->status === 'verified_fail')
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill display-1 text-danger"></i>
                        </div>
                        <h2 class="text-danger mb-3">
                            <i class="bi bi-x-circle-fill"></i> TIDAK LULUS
                        </h2>
                        <p class="text-muted">Maaf, Anda belum lulus ujian ini. Silakan coba lagi.</p>
                    @else
                        <div class="mb-4">
                            <i class="bi bi-hourglass-split display-1 text-warning"></i>
                        </div>
                        <h2 class="text-warning mb-3">
                            <i class="bi bi-hourglass-split"></i> MENUNGGU VERIFIKASI
                        </h2>
                        <p class="text-muted mb-2">Ujian Anda sudah berhasil dikumpulkan dan sedang dalam proses verifikasi oleh admin.</p>
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Informasi:</strong> Admin akan segera memverifikasi hasil ujian Anda. 
                            Anda akan mendapatkan notifikasi setelah proses verifikasi selesai. 
                            Silakan cek halaman ini secara berkala untuk melihat hasilnya.
                        </div>
                    @endif

                    {{-- Score Display --}}
                    @if(!in_array($session->status, ['submitted']))
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-4">
                            <div class="bg-light rounded p-4">
                                <h1 class="display-4 fw-bold mb-0 
                                    @if($session->status === 'verified_pass') text-success 
                                    @elseif($session->status === 'verified_fail') text-danger 
                                    @else text-secondary @endif">
                                    {{ $session->score ?? 0 }}%
                                </h1>
                                <p class="text-muted mb-0">Nilai Anda</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded p-4">
                                <h1 class="display-4 fw-bold mb-0 text-secondary">
                                    {{ $session->exam->passing_score ?? 0 }}%
                                </h1>
                                <p class="text-muted mb-0">Nilai Lulus (KKM)</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Details Card --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Ujian</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Nama Ujian</td>
                            <td><strong>{{ $session->exam->title ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Skill</td>
                            <td><span class="badge bg-info">{{ $session->exam->skill->name ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Target Level</td>
                            <td><span class="badge bg-secondary">Level {{ $session->exam->target_level ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status Ujian</td>
                            <td>
                                @if($session->status === 'assigned')
                                    <span class="badge bg-warning">Belum Dimulai</span>
                                @elseif($session->status === 'started')
                                    <span class="badge bg-info">Sedang Dikerjakan</span>
                                @elseif($session->status === 'submitted')
                                    <span class="badge bg-secondary">Menunggu Verifikasi Admin</span>
                                @elseif($session->status === 'verified_pass')
                                    <span class="badge bg-primary">Lulus - Menunggu Persetujuan Manager</span>
                                @elseif($session->status === 'approved')
                                    <span class="badge bg-success">Lulus - Disetujui Manager</span>
                                @elseif($session->status === 'rejected')
                                    <span class="badge bg-dark">Lulus - Ditolak Manager</span>
                                @elseif($session->status === 'verified_fail')
                                    <span class="badge bg-danger">Tidak Lulus - Terverifikasi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Mulai</td>
                            <td>{{ $session->started_at ? $session->started_at->format('d M Y, H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Selesai</td>
                            <td>{{ $session->finished_at ? $session->finished_at->format('d M Y, H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Durasi Pengerjaan</td>
                            <td>
                                @if($session->started_at && $session->finished_at)
                                    @php
                                        $diffInSeconds = $session->started_at->diffInSeconds($session->finished_at);
                                        $hours = floor($diffInSeconds / 3600);
                                        $minutes = floor(($diffInSeconds % 3600) / 60);
                                        $seconds = $diffInSeconds % 60;
                                    @endphp
                                    @if($hours > 0)
                                        {{ $hours }} jam {{ $minutes }} menit {{ $seconds }} detik
                                    @elseif($minutes > 0)
                                        {{ $minutes }} menit {{ $seconds }} detik
                                    @else
                                        {{ $seconds }} detik
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @if($session->verified_at)
                        <tr>
                            <td class="text-muted">Diverifikasi Pada</td>
                            <td>{{ $session->verified_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @endif
                        @if($session->verifier)
                        <tr>
                            <td class="text-muted">Diverifikasi Oleh</td>
                            <td>{{ $session->verifier->name ?? '-' }}</td>
                        </tr>
                        @endif
                        @if($session->decided_at)
                        <tr>
                            <td class="text-muted">Keputusan Manager</td>
                            <td>
                                @if($session->manager_decision === 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($session->manager_decision === 'rejected')
                                    <span class="badge bg-dark">Ditolak</span>
                                @endif
                                <br><small class="text-muted">{{ $session->decided_at->format('d M Y, H:i') }}</small>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Admin Notes (if any) --}}
            @if($session->admin_notes && in_array($session->status, ['verified_pass', 'verified_fail', 'approved', 'rejected']))
            <div class="card">
                <div class="card-header bg-light-info">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-left-text"></i> Catatan dari Admin
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong>
                        <p class="mb-0 mt-2">{{ $session->admin_notes }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Action --}}
            <div class="text-center">
                <a href="{{ route('user.my-training') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-left"></i> Kembali ke Pelatihan Saya
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
