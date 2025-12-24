@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Test Database Users</h4>
                </div>
                <div class="card-body">
                    @php
                        $users = \App\Models\User::all();
                    @endphp

                    @if($users->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr>

                        <h5>Test Login:</h5>
                        <p><strong>Admin:</strong> admin@example.com / admin123</p>
                        <p><strong>User:</strong> user@example.com / user123</p>

                        <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>
                    @else
                        <div class="alert alert-danger">
                            <strong>No users found!</strong>
                            <p>Please run: <code>php artisan migrate:fresh --seed</code></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
