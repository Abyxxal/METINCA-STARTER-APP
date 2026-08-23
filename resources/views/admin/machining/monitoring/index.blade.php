@extends('layouts.app')

@section('title', 'Machining - Monitoring')

@push('styles')
@endpush

@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Machining Monitoring</h3>
                    <p class="text-subtitle text-muted">Monitoring status mesin secara real-time</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Machining Monitoring</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="row">
            {{-- Main --}}
            <div class="col-12 col-lg-9">
                <div class="row g-3">
                    <div class="col-12 col-lg-3 col-md-6">
                        <div class="kpi-card">
                            <i class="bi bi-cpu kpi-icon kpi-info"></i>
                            <div>
                                <div class="kpi-value">16</div>
                                <div class="kpi-label">Total Machines</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="empty-state">
                                    <i class="bi bi-speedometer2"></i>
                                    <h5>Monitoring belum tersedia</h5>
                                    <p class="text-muted mb-0">Modul ini sedang dalam pengembangan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aside --}}
            <div class="col-12 col-lg-3">

            </div>
        </div>
    </div>

@endsection
