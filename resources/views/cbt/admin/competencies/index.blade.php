@extends('layouts.app')

@section('title', 'Kelola Level Skill Karyawan')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kelola Level Skill Karyawan</h3>
                <p class="text-subtitle text-muted">Edit manual level skill setiap karyawan</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Level Skill Karyawan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="card-title mb-0">Daftar Karyawan</h4>
                </div>
                <div class="col-md-4">
                    <form method="GET" id="divisionFilterForm">
                        <select name="division_id" class="form-select" onchange="window.autoSubmitForm(this.form)">
                            <option value="">-- Semua Divisi --</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }} ({{ $division->department->name }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Divisi</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>{{ $employee->nik }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->division->name ?? '-' }}</td>
                                <td>{{ $employee->position->name ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('cbt.admin.employee-competencies.edit', $employee) }}" 
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i> Edit Level Skill
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada karyawan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $employees->links() }}
        </div>
    </div>
</section>
@endsection
