@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Debug - Check Current User Role</h4>
                </div>
                <div class="card-body">
                    @auth
                        @php
                            $user = auth()->user();
                        @endphp

                        <div class="alert alert-info">
                            <h5>Logged In User Info:</h5>
                            <p><strong>ID:</strong> {{ $user->id }}</p>
                            <p><strong>Name:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Role:</strong> <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'success' }}">{{ $user->role }}</span></p>
                            <p><strong>Created At:</strong> {{ $user->created_at }}</p>
                        </div>

                        @if($user->role === 'admin')
                            <div class="alert alert-success">
                                ✓ You are ADMIN - Should have access to all admin menus
                            </div>

                            <h5 class="mt-4">Admin Menu Links (for testing):</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="{{ route('master-data') }}">Master Data</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('material-management') }}">Material Management</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('evaluation-and-exam') }}">Evaluation & Exam</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('socialization-and-news') }}">Socialization & News</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('report-and-audit') }}">Report & Audit</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('settings') }}">Settings</a>
                                </li>
                            </ul>
                        @else
                            <div class="alert alert-warning">
                                ⚠ You are USER - Cannot access admin menus
                            </div>
                        @endif
                    @else
                        <div class="alert alert-danger">
                            ✗ Not logged in - Please <a href="{{ route('login') }}">login</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
