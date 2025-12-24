@extends('layouts.guest')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Manual Setup - Create Users</h4>
                </div>
                <div class="card-body">
                    @php
                        $users = \App\Models\User::all();
                    @endphp

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-4">Existing Users ({{ $users->count() }})</h5>
                            @if($users->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Email</th>
                                                <th>Name</th>
                                                <th>Role</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'success' }}">
                                                            {{ $user->role }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    No users found. Create one below.
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-4">Create New User</h5>
                            <form method="POST" action="{{ route('setup.store-user') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                        id="name" name="name" value="{{ old('name', 'Admin User') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                        id="email" name="email" value="{{ old('email', 'admin@example.com') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                        id="password" name="password" value="admin123" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-success btn-block w-100">
                                    Create User
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <h5>Quick Create Template</h5>
                    <p class="text-muted">You can also create users quickly:</p>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <form method="POST" action="{{ route('setup.store-user') }}" style="display: inline;">
                                @csrf
                                <input type="hidden" name="name" value="Admin User">
                                <input type="hidden" name="email" value="admin@example.com">
                                <input type="hidden" name="password" value="admin123">
                                <input type="hidden" name="role" value="admin">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Create Admin (admin@example.com / admin123)
                                </button>
                            </form>
                        </div>

                        <div class="col-md-6 mb-2">
                            <form method="POST" action="{{ route('setup.store-user') }}" style="display: inline;">
                                @csrf
                                <input type="hidden" name="name" value="User Karyawan">
                                <input type="hidden" name="email" value="user@example.com">
                                <input type="hidden" name="password" value="user123">
                                <input type="hidden" name="role" value="user">
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    Create User (user@example.com / user123)
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-info mt-3">
                        <strong>Ready to Login?</strong>
                        <p class="mb-0">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Go to Login Page</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
