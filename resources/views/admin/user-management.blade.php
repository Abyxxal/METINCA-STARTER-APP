@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Manajemen User</h3>
                <p class="text-subtitle text-muted">Kelola akun dan role pengguna sistem</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manajemen User</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs px-3 pt-3" id="userTabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'staff' ? 'active' : '' }}"
                       href="{{ route('admin.users.index', ['tab' => 'staff']) }}">
                        <i class="bi bi-person-badge"></i> Supervisor & Manager
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'employee' ? 'active' : '' }}"
                       href="{{ route('admin.users.index', ['tab' => 'employee']) }}">
                        <i class="bi bi-people"></i> Karyawan
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">

            {{-- ===== TAB SUPERVISOR & MANAGER ===== --}}
            @if($activeTab === 'staff')
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3 mt-3">
                    <input type="hidden" name="tab" value="staff">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="text" name="staff_search" class="form-control"
                               placeholder="Cari nama atau email..."
                               value="{{ request('staff_search') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        @if(request('staff_search'))
                            <a href="{{ route('admin.users.index', ['tab' => 'staff']) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role Saat Ini</th>
                                <th>Ubah Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffUsers as $i => $user)
                                <tr>
                                    <td>{{ $staffUsers->firstItem() + $i }}</td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === Auth::id())
                                            <span class="badge bg-warning text-dark ms-1">Anda</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-primary"><i class="bi bi-shield-check"></i> Supervisor</span>
                                        @elseif($user->role === 'manager')
                                            <span class="badge bg-success"><i class="bi bi-person-badge"></i> Manager</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->id === Auth::id())
                                            <span class="text-muted small"><i class="bi bi-lock"></i> Akun Anda</span>
                                        @else
                                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-flex gap-2 align-items-center">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="tab" value="staff">
                                                <select name="role" class="form-select form-select-sm" style="width: 150px;">
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Supervisor</option>
                                                    <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-check-lg"></i> Simpan
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                        Tidak ada Supervisor/Manager ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $staffUsers->appends(array_merge(request()->query(), ['tab' => 'staff']))->links() }}
            @endif

            {{-- ===== TAB KARYAWAN ===== --}}
            @if($activeTab === 'employee')
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3 mt-3">
                    <input type="hidden" name="tab" value="employee">
                    <div class="input-group" style="max-width: 400px;">
                        <input type="text" name="employee_search" class="form-control"
                               placeholder="Cari nama atau email..."
                               value="{{ request('employee_search') }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        @if(request('employee_search'))
                            <a href="{{ route('admin.users.index', ['tab' => 'employee']) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role Saat Ini</th>
                                <th>Ubah Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employeeUsers as $i => $user)
                                <tr>
                                    <td>{{ $employeeUsers->firstItem() + $i }}</td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary"><i class="bi bi-person"></i> Karyawan</span>
                                    </td>
                                    <td>
                                        @if($user->id === Auth::id())
                                            <span class="text-muted small"><i class="bi bi-lock"></i> Akun Anda</span>
                                        @else
                                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-flex gap-2 align-items-center">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="tab" value="employee">
                                                <select name="role" class="form-select form-select-sm" style="width: 150px;">
                                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Karyawan</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Supervisor</option>
                                                    <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-check-lg"></i> Simpan
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        Tidak ada karyawan ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $employeeUsers->appends(array_merge(request()->query(), ['tab' => 'employee']))->links() }}
            @endif
        </div>
    </div>

</section>
@endsection
