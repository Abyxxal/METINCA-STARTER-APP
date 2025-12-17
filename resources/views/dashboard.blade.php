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

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dashboard Training Management</h3>
                    <p class="text-subtitle text-muted">Monitoring sistem pelatihan karyawan Metinca</p>
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
            <!-- 4 Kartu Statistik -->
            <div class="col-12">
                <div class="row">
                    <!-- Total Karyawan Aktif -->
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
                                        <h6 class="font-extrabold mb-0">485</h6>
                                        <small class="text-muted">dari 500 karyawan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Materi/SOP Tersedia -->
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
                                        <h6 class="text-muted font-semibold">Materi/SOP Tersedia</h6>
                                        <h6 class="font-extrabold mb-0">128</h6>
                                        <small class="text-muted">45 SOP + 83 Materi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Belum Lulus Ujian (Warning) -->
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
                                        <h6 class="text-muted font-semibold">Belum Lulus Ujian</h6>
                                        <h6 class="font-extrabold mb-0 text-danger">37</h6>
                                        <small class="text-danger">Perlu remedial!</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sertifikat Expired Bulan Ini -->
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="bi bi-calendar-x-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Sertifikat Expired</h6>
                                        <h6 class="font-extrabold mb-0">12</h6>
                                        <small class="text-muted">Bulan ini (Jan 2025)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik dan Tabel -->
            <div class="col-12">
                <div class="row">
                    <!-- Bar Chart: Rata-rata Nilai Ujian per Departemen -->
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Rata-rata Nilai Ujian per Departemen</h4>
                                <p class="text-muted small mb-0">Data periode Januari 2025</p>
                            </div>
                            <div class="card-body">
                                <div id="chartNilaiDepartemen"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Mini: 5 Aktivitas Terakhir -->
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
                                            <tr>
                                                <td class="text-center" style="width: 40px;">
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </span>
                                                </td>
                                                <td>
                                                    <p class="mb-0"><strong>Budi Santoso</strong> lulus ujian <strong>GMP</strong></p>
                                                    <small class="text-muted">2 menit yang lalu</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                                    </span>
                                                </td>
                                                <td>
                                                    <p class="mb-0"><strong>Admin DC</strong> upload SOP <strong>WI-QC-012 Rev. 3</strong></p>
                                                    <small class="text-muted">15 menit yang lalu</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </span>
                                                </td>
                                                <td>
                                                    <p class="mb-0"><strong>Sarah HR</strong> update data karyawan <strong>NIK 2024042</strong></p>
                                                    <small class="text-muted">1 jam yang lalu</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </span>
                                                </td>
                                                <td>
                                                    <p class="mb-0"><strong>Ahmad Rifai</strong> lulus ujian <strong>Safety Training</strong></p>
                                                    <small class="text-muted">2 jam yang lalu</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </span>
                                                </td>
                                                <td>
                                                    <p class="mb-0"><strong>Dewi Lestari</strong> gagal ujian <strong>Quality Control</strong></p>
                                                    <small class="text-muted">3 jam yang lalu</small>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="px-4 mt-3">
                                    <a href="{{ route('report-and-audit') }}#riwayatpelatihan" class="btn btn-sm btn-primary w-100">
                                        <i class="bi bi-eye me-1"></i>Lihat Semua Aktivitas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="col-12">
                <div class="row">
                    <!-- Quick Links -->
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Quick Access</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('material-management') }}" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-collection-fill d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Material Management</span>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('evaluation-and-exam') }}" class="btn btn-outline-success w-100">
                                            <i class="bi bi-journal-text d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Evaluation & Exam</span>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('socialization-and-news') }}" class="btn btn-outline-warning w-100">
                                            <i class="bi bi-megaphone-fill d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Socialization</span>
                                        </a>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <a href="{{ route('report-and-audit') }}" class="btn btn-outline-info w-100">
                                            <i class="bi bi-file-earmark-bar-graph-fill d-block" style="font-size: 2rem;"></i>
                                            <span class="d-block mt-2">Report & Audit</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Training Status Overview -->
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">Status Training Overview</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>GMP Training</span>
                                        <span class="text-success fw-bold">92%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 92%;" 
                                            aria-valuenow="92" aria-valuemin="0" aria-valuemax="100">92% Lulus</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>5R/5S Training</span>
                                        <span class="text-success fw-bold">88%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 88%;" 
                                            aria-valuenow="88" aria-valuemin="0" aria-valuemax="100">88% Lulus</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Safety Training</span>
                                        <span class="text-warning fw-bold">75%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 75%;" 
                                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75% Lulus</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Quality Control</span>
                                        <span class="text-danger fw-bold">65%</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 65%;" 
                                            aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">65% Lulus</div>
                                    </div>
                                </div>
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Quality Control memerlukan perhatian khusus untuk meningkatkan passing rate.
                                </div>
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
        // Bar Chart: Rata-rata Nilai Ujian per Departemen
        var optionsNilaiDepartemen = {
            series: [{
                name: 'Rata-rata Nilai',
                data: [88, 92, 75, 65, 82, 90, 78, 85]
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
                categories: ['Production', 'Quality Control', 'Maintenance', 'Warehouse', 'Engineering', 'HR', 'Finance', 'Purchasing'],
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
    </script>
@endpush