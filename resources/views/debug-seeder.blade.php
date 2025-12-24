@extends('layouts.guest')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Database Debug - Check Seeder</h4>
                </div>
                <div class="card-body">
                    @php
                        use App\Models\User;
                        use App\Models\Department;
                        use App\Models\Position;
                        use App\Models\Employee;
                    @endphp

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-left border-primary">
                                <div class="card-body">
                                    <h6 class="card-title">Users Table</h6>
                                    <p class="mb-0">Count: <strong>{{ User::count() }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left border-success">
                                <div class="card-body">
                                    <h6 class="card-title">Departments Table</h6>
                                    <p class="mb-0">Count: <strong>{{ Department::count() }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left border-warning">
                                <div class="card-body">
                                    <h6 class="card-title">Positions Table</h6>
                                    <p class="mb-0">Count: <strong>{{ Position::count() }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left border-danger">
                                <div class="card-body">
                                    <h6 class="card-title">Employees Table</h6>
                                    <p class="mb-0">Count: <strong>{{ Employee::count() }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>Users Detail</h5>
                    @if(User::count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(User::all() as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'success' }}">
                                                    {{ $user->role }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">No users found</div>
                    @endif

                    <hr>

                    <h5>Run Seeders</h5>
                    <form method="POST" action="{{ route('debug.seed') }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">
                            Create Departments, Positions, Employees & Users
                        </button>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger mt-3">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
