{{-- Inlcude layout utama (Sidebar dan footer) --}}
@extends('layouts.app')

{{-- Set title berdasarkan page --}}
@section('title', 'Dashboard')

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
        <section class="row gy-4 gx-4">
            {{-- SECTION: 4 Kartu Statistik Utama --}}
            {{-- Fungsi: Menampilkan KPI (Key Performance Indicator) dari sistem training --}}
            {{-- Isi: Total Karyawan Aktif, Materi Tersedia, User Belum Lulus, Sertifikat Expired --}}
            <div class="col-12">
                <div class="row g-3">
                    {{-- Kartu 1: Total Karyawan Aktif --}}
                    {{-- Menunjukkan jumlah karyawan yang aktif dalam sistem training --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <a href="{{ route('master-data') }}" class="kpi-card" aria-label="Total karyawan aktif: {{ $stats['total_employees'] }}">
                            <i class="bi bi-people-fill kpi-icon kpi-primary"></i>
                            <div>
                                <div class="kpi-value" id="total-employees">{{ $stats['total_employees'] }}</div>
                                <div class="kpi-label">Total Karyawan Aktif</div>
                            </div>
                        </a>
                    </div>

                    {{-- Kartu 2: Role-aware --}}
                    {{-- Manager  : Pending Persetujuan Level (antrean approval) --}}
                    {{-- Supervisor: Total Soal Aktif dalam bank soal --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        @if(Auth::user()->isManager())
                        <a href="{{ route('cbt.admin.sessions.pending-approval') }}" class="kpi-card">
                            <i class="bi bi-patch-check-fill kpi-icon kpi-warning"></i>
                            <div>
                                <div class="kpi-value {{ $stats['pending_approval'] > 0 ? 'text-warning' : '' }}" id="pending-approval">{{ $stats['pending_approval'] }}</div>
                                <div class="kpi-label">Pending Persetujuan</div>
                            </div>
                        </a>
                        @else
                        <a href="{{ route('cbt.admin.questions.index') }}" class="kpi-card">
                            <i class="bi bi-file-earmark-text-fill kpi-icon kpi-success"></i>
                            <div>
                                <div class="kpi-value" id="total-questions">{{ $stats['total_questions'] }}</div>
                                <div class="kpi-label">Total Soal Aktif</div>
                            </div>
                        </a>
                        @endif
                    </div>

                    {{-- Kartu 3: Ujian Pending Verifikasi --}}
                    {{-- Menunjukkan jumlah ujian yang sudah dikerjakan tapi belum diverifikasi admin --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <a href="{{ route('cbt.admin.sessions.pending') }}" class="kpi-card">
                            <i class="bi bi-exclamation-triangle-fill kpi-icon kpi-danger"></i>
                            <div>
                                <div class="kpi-value text-warning" id="pending-verification">{{ $stats['pending_verification'] }}</div>
                                <div class="kpi-label">Pending Verifikasi</div>
                            </div>
                        </a>
                    </div>

                    {{-- Kartu 4: Ujian Aktif Bulan Ini --}}
                    {{-- Menunjukkan jumlah sesi ujian yang sedang berjalan bulan ini --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <a href="{{ route('cbt.admin.sessions.index') }}" class="kpi-card">
                            <i class="bi bi-calendar-check-fill kpi-icon kpi-info"></i>
                            <div>
                                <div class="kpi-value" id="active-exams">{{ $stats['active_exams_this_month'] }}</div>
                                <div class="kpi-label">Ujian Aktif &mdash; {{ now()->format('M Y') }}</div>
                            </div>
                        </a>
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
                                <p class="text-muted small mb-0">Periode {{ now()->format('M Y') }}</p>
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
                                                    <span class="badge bg-success" title="Lulus" aria-label="Lulus"><i class="bi bi-check-circle-fill" aria-hidden="true"></i></span>
                                                    @elseif($activity->status === 'verified_fail')
                                                    <span class="badge bg-danger" title="Tidak Lulus" aria-label="Tidak Lulus"><i class="bi bi-x-circle-fill" aria-hidden="true"></i></span>
                                                    @elseif($activity->status === 'submitted')
                                                    <span class="badge bg-info" title="Menunggu Verifikasi" aria-label="Menunggu Verifikasi"><i class="bi bi-clipboard-check-fill" aria-hidden="true"></i></span>
                                                    @elseif($activity->status === 'started')
                                                    <span class="badge bg-warning" title="Sedang Ujian" aria-label="Sedang Ujian"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
                                                    @else
                                                    <span class="badge bg-secondary" title="Ditugaskan" aria-label="Ditugaskan"><i class="bi bi-circle-fill" aria-hidden="true"></i></span>
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
                                                <td colspan="2" class="p-0">
                                                    <div class="empty-state m-3">
                                                        <i class="bi bi-inbox"></i>
                                                        <h5>Belum ada aktivitas</h5>
                                                        <p class="text-muted mb-0">Aktivitas ujian terbaru akan tampil di sini</p>
                                                    </div>
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
                                <div class="row g-3">
                                    {{-- Tile Master Data / Riwayat Persetujuan (role-aware) --}}
                                    <div class="col-6">
                                        @if(Auth::user()->isManager())
                                        <a href="{{ route('cbt.admin.sessions.approval-history') }}" class="quick-tile">
                                            <i class="bi bi-clock-history kpi-icon kpi-primary"></i>
                                            <span class="quick-tile-label">Riwayat Persetujuan</span>
                                        </a>
                                        @else
                                        <a href="{{ route('master-data') }}" class="quick-tile">
                                            <i class="bi bi-database-fill kpi-icon kpi-primary"></i>
                                            <span class="quick-tile-label">Master Data</span>
                                        </a>
                                        @endif
                                    </div>
                                    {{-- Tile Bank Soal --}}
                                    <div class="col-6">
                                        <a href="{{ route('cbt.admin.questions.index') }}" class="quick-tile">
                                            <i class="bi bi-journal-text kpi-icon kpi-success"></i>
                                            <span class="quick-tile-label">Bank Soal</span>
                                        </a>
                                    </div>
                                    {{-- Tile Sesi Ujian --}}
                                    <div class="col-6">
                                        <a href="{{ route('cbt.admin.sessions.index') }}" class="quick-tile">
                                            <i class="bi bi-clipboard-check-fill kpi-icon kpi-warning"></i>
                                            <span class="quick-tile-label">Sesi Ujian</span>
                                        </a>
                                    </div>
                                    {{-- Tile Matriks Kompetensi --}}
                                    <div class="col-6">
                                        <a href="{{ route('cbt.admin.competency-matrix') }}" class="quick-tile">
                                            <i class="bi bi-grid-3x3-gap-fill kpi-icon kpi-info"></i>
                                            <span class="quick-tile-label">Matriks Kompetensi</span>
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
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h5>Belum ada data ujian</h5>
                                    <p class="text-muted mb-0">Statistik kelulusan skill akan tampil setelah ada ujian yang selesai</p>
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
        // Bar Chart: Rata-rata Nilai Ujian per Skill (theme-aware)
        function chartTheme() {
            var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            return {
                bar: dark ? '#6ea8fe' : '#435ebe',
                label: dark ? '#ced4da' : '#304758',
                gridBorder: dark ? '#495057' : '#e7e7e7',
                rowColors: dark ? ['rgba(255,255,255,.04)', 'transparent'] : ['#f3f3f3', 'transparent']
            };
        }

        function buildChartOptions() {
            var t = chartTheme();
            return {
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
                colors: [t.bar],
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
                        colors: [t.label]
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
                    max: 100,
                    labels: {
                        style: {
                            colors: t.label
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    theme: document.documentElement.getAttribute('data-bs-theme'),
                    y: {
                        formatter: function (val) {
                            return val + "%"
                        }
                    }
                },
                grid: {
                    borderColor: t.gridBorder,
                    row: {
                        colors: t.rowColors,
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
        }

        var chartNilaiDepartemen = new ApexCharts(document.querySelector("#chartNilaiDepartemen"), buildChartOptions());
        chartNilaiDepartemen.render();

        new MutationObserver(function () {
            if (typeof chartNilaiDepartemen !== 'undefined') {
                chartNilaiDepartemen.updateOptions(buildChartOptions());
            }
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });

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