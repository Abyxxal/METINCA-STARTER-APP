@extends('layouts.guest')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Password Hash Test</h4>
                </div>
                <div class="card-body">
                    @php
                        use App\Models\User;
                        use Illuminate\Support\Facades\Hash;
                    @endphp

                    <h5 class="mb-3">Users in Database</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Password Hash</th>
                                    <th>Test Password</th>
                                    <th>Match?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(User::all() as $user)
                                    @php
                                        $testPassword = $user->email === 'admin@example.com' ? 'admin123' : 'user123';
                                        $isMatch = Hash::check($testPassword, $user->password);
                                    @endphp
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><code style="font-size: 10px;">{{ substr($user->password, 0, 20) }}...</code></td>
                                        <td><code>{{ $testPassword }}</code></td>
                                        <td>
                                            <span class="badge bg-{{ $isMatch ? 'success' : 'danger' }}">
                                                {{ $isMatch ? '✓ YES' : '✗ NO' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-danger">
                                            <strong>No users found in database!</strong>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h5 class="mb-3">Test Login Attempt</h5>
                    <form method="POST" action="{{ route('login.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                value="admin@example.com" required>
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                value="admin123" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg">Test Login</button>
                            <a href="{{ route('setup.show') }}" class="btn btn-secondary btn-lg">Back to Setup</a>
                        </div>
                    </form>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            @foreach ($errors->all() as $error)
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
