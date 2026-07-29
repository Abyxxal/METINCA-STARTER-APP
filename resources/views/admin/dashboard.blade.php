{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Dashbaord')

{{-- Untuk menggunakan css --}}
@push('styles')
    {{-- contoh --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/static/css/pages/dashboard.css') }}"> --}}
@endpush

{{-- Isi content --}}
@section('content')

    {{-- SECTION: Page Header --}}
    {{-- Fungsi: Menampilkan judul halaman, deskripsi, dan breadcrumb navigasi --}}
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dashboard</h3>
                    <p class="text-subtitle text-muted">Monitoring sistem ujian dan kompetensi karyawan Metinca</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            {{-- SECTION: 4 Kartu Statistik Utama --}}
            {{-- Fungsi: Menampilkan KPI (Key Performance Indicator) dari sistem training --}}
            {{-- Isi: Total Karyawan Aktif, Materi Tersedia, User Belum Lulus, Sertifikat Expired --}}
            <div class="col-12">
                <div class="row">
                    {{-- Kartu 1: Total Karyawan Aktif --}}
                    {{-- Menunjukkan jumlah karyawan yang aktif dalam sistem training --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Karyawan Aktif</h6>
                                        <h6 class="font-extrabold mb-0" id="total-employees">{{ $stats['total_employees'] }}</h6>
                                        <small class="text-muted">Karyawan aktif</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kartu 2: Total Soal Aktif --}}
                    {{-- Menunjukkan jumlah soal dalam bank soal yang aktif --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="bi bi-file-earmark-text-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Soal Aktif</h6>
                                        <h6 class="font-extrabold mb-0" id="total-questions">{{ $stats['total_questions'] }}</h6>
                                        <small class="text-muted">Bank Soal CBT</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kartu 3: Ujian Pending Verifikasi --}}
                    {{-- Menunjukkan jumlah ujian yang sudah dikerjakan tapi belum diverifikasi admin --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon red mb-2">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Pending Verifikasi</h6>
                                        <h6 class="font-extrabold mb-0 text-warning" id="pending-verification">{{ $stats['pending_verification'] }}</h6>
                                        <small class="text-warning">Perlu diverifikasi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kartu 4: Ujian Aktif Bulan Ini --}}
                    {{-- Menunjukkan jumlah sesi ujian yang sedang berjalan bulan ini --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="bi bi-calendar-check-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Ujian Aktif</h6>
                                        <h6 class="font-extrabold mb-0" id="active-exams">{{ $stats['active_exams_this_month'] }}</h6>
                                        <small class="text-muted">Bulan ini ({{ now()->format('M Y') }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Grafik dan Tabel Mini --}}
            {{-- Fungsi: Menampilkan analisis visual dan ringkasan aktivitas terbaru --}}
            <div class="col-12">
                <div class="row">
                    {{-- Sub-section: Bar Chart Nilai Ujian per Skill --}}
                    {{-- Nama: Rata-rata Nilai Ujian per Skill --}}
                    {{-- Fungsi: Visualisasi perbandingan rata-rata nilai ujian per skill untuk melihat skill mana yang perlu improvement --}}
                    {{-- Chart ID: #chartNilaiDepartemen (dirender menggunakan ApexCharts) --}}
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Rata-rata Nilai Ujian per Skill</h4>
                                <p class="text-muted small mb-0">Data periode Februari 2026</p>
                            </div>
                            <div class="card-body">
                                <div id="chartNilaiDepartemen"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-section: Tabel Mini Aktivitas Terakhir --}}
                    {{-- Nama: Aktivitas Terakhir --}}
                    {{-- Fungsi: Menampilkan 5 aktivitas terbaru dalam sistem (lulus ujian, upload dokumen, update data, etc) --}}
                    {{-- Isi: Daftar aktivitas dengan badge status, nama user, action, dan waktu --}}
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Aktivitas Terakhir</h4>
                                <p class="text-muted small mb-0">5 aktivitas terbaru</p>
                            </div>
                            <div class="card-body px-0">
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            @forelse($recentActivities as $activity)
                                            <tr>
                                                <td class="text-center" style="width: 40px;">
                                                    @if($activity->status === 'verified_pass')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </span>
                                                    @elseif($activity->status === 'verified_fail')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </span>
                                                    @elseif($activity->status === 'submitted')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-clipboard-check-fill"></i>
                                                    </span>
                                                    @elseif($activity->status === 'started')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-hourglass-split"></i>
                                                    </span>
                                                    @else
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-circle-fill"></i>
                                                    </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <p class="mb-0">
                                                        <strong>{{ $activity->employee->name ?? 'Unknown' }}</strong> 
                                                        @if($activity->status === 'verified_pass')
                                                            lulus ujian
                                                        @elseif($activity->status === 'verified_fail')
                                                            tidak lulus ujian
                                                        @elseif($activity->status === 'submitted')
                                                            menyelesaikan ujian
                                                        @elseif($activity->status === 'started')
                                                            sedang mengerjakan ujian
                                                        @else
                                                            ditugaskan ujian
                                                        @endif
                                                        <strong>{{ $activity->exam->title ?? 'Unknown Exam' }}</strong>
                                                    </p>
                                                    <small class="text-muted">{{ $activity->updated_at->diffForHumans() }}</small>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    Belum ada aktivitas
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                {{-- Tombol untuk melihat semua sesi ujian --}}
                                <div class="px-4 mt-3">
                                    <a href="{{ route('cbt.admin.sessions.index') }}" class="btn btn-sm btn-primary w-100">
                                        <i class="bi bi-eye me-1"></i>Lihat Semua Sesi Ujian
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION: Informasi Tambahan & Quick Access --}}
            {{-- Fungsi: Menyediakan navigasi cepat ke modul-modul utama dan overview status training --}}
            <div class="col-12">
                <div class="row">
                    {{-- Sub-section: Quick Access Buttons --}}
                    {{-- Nama: Quick Access --}}
                    {{-- Fungsi: Tombol navigasi cepat ke 4 modul utama sistem --}}
                    {{-- Isi: 4 button besar dengan icon (Material Management, Evaluation, Socialization, Report & Audit) --}}
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Quick Access</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Button ke Master Data --}}
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('master-data') }}" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-database-fill d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Master Data</span>
                                        </a>
                                    </div>
                                    {{-- Button ke Bank Soal --}}
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('cbt.admin.questions.index') }}" class="btn btn-outline-success w-100">
                                            <i class="bi bi-journal-text d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Bank Soal</span>
                                        </a>
                                    </div>
                                    {{-- Button ke Sesi Ujian --}}
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('cbt.admin.sessions.index') }}" class="btn btn-outline-warning w-100">
                                            <i class="bi bi-clipboard-check-fill d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Sesi Ujian</span>
                                        </a>
                                    </div>
                                    {{-- Button ke Matriks Kompetensi --}}
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('cbt.admin.competency-matrix') }}" class="btn btn-outline-info w-100">
                                            <i class="bi bi-grid-3x3-gap-fill d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Matriks Kompetensi</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-section: Skill Status Overview dengan Progress Bar --}}
                    {{-- Nama: Status Skill Overview --}}
                    {{-- Fungsi: Menampilkan persentase kelulusan untuk setiap skill --}}
                    {{-- Isi: Progress bar untuk CMM, PT, MPL, RT dengan warna berbeda (hijau=baik, kuning=sedang, merah=perlu improvement) --}}
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Status Skill Overview</h4>
                            </div>
                            <div class="card-body">
                                @forelse($skillPassingRates as $skill)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ $skill->code }} - {{ $skill->name }}</span>
                                        <span class="fw-bold
                                            @if($skill->pass_rate >= 80) text-success
                                            @elseif($skill->pass_rate >= 70) text-warning
                                            @else text-danger
                                            @endif
                                        ">{{ $skill->pass_rate }}%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar
                                            @if($skill->pass_rate >= 80) bg-success
                                            @elseif($skill->pass_rate >= 70) bg-warning
                                            @else bg-danger
                                            @endif
                                        " role="progressbar" style="width: {{ $skill->pass_rate }}%;" 
                                            aria-valuenow="{{ $skill->pass_rate }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $skill->pass_rate }}% Lulus ({{ $skill->passed }}/{{ $skill->total }})
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data ujian
                                </div>
                                @endforelse
                                
                                @if($skillPassingRates->isNotEmpty() && $skillPassingRates->last()->pass_rate < 70)
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    {{ $skillPassingRates->last()->code }} ({{ $skillPassingRates->last()->name }}) memerlukan perhatian khusus untuk meningkatkan passing rate.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

{{-- Untuk menggunakan js --}}
@push('scripts')
    <script src="{{ asset('assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        // Bar Chart: Rata-rata Nilai Ujian per Skill
        var optionsNilaiDepartemen = {
            series: [{
                name: 'Rata-rata Nilai',
                data: {!! json_encode($skillStats->pluck('avg_score')->toArray()) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            colors: ['#435ebe'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded',
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val + "%";
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            xaxis: {
                categories: {!! json_encode($skillStats->pluck('code')->toArray()) !!},
                position: 'bottom',
                labels: {
                    rotate: -45,
                    rotateAlways: true
                }
            },
            yaxis: {
                title: {
                    text: 'Nilai Rata-rata (%)'
                },
                min: 0,
                max: 100
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + "%"
                    }
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            annotations: {
                yaxis: [{
                    y: 70,
                    borderColor: '#FF4560',
                    label: {
                        borderColor: '#FF4560',
                        style: {
                            color: '#fff',
                            background: '#FF4560',
                        },
                        text: 'Passing Grade: 70%',
                    }
                }]
            }
        };

        var chartNilaiDepartemen = new ApexCharts(document.querySelector("#chartNilaiDepartemen"), optionsNilaiDepartemen);
        chartNilaiDepartemen.render();

        // Realtime dashboard update via Pusher
        var pusherDashboard = new Pusher('{{ env("REVERB_APP_KEY") }}', {
            wsHost: '{{ env("REVERB_HOST", "localhost") }}',
            wsPort: {{ env("REVERB_PORT", 8080) }},
            wssPort: {{ env("REVERB_PORT", 8080) }},
            forceTLS: false,
            encrypted: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        var dashChannel = pusherDashboard.subscribe('admin.dashboard');
        dashChannel.bind('App\\Events\\DashboardStatsUpdated', function(data) {
            var el = document.getElementById('total-employees');
            if (el) el.textContent = data.total_employees;

            el = document.getElementById('total-questions');
            if (el) el.textContent = data.total_questions;

            el = document.getElementById('pending-verification');
            if (el) el.textContent = data.pending_verification;

            el = document.getElementById('active-exams');
            if (el) el.textContent = data.active_exams_this_month;
        });
    </script>
@endpush