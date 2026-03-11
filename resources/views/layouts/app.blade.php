<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <title>@yield('title') - Metinca</title>



    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="shortcut icon"
        href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC"
        type="image/png">

    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .logo img {
            width: 50px !important;
            height: auto !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="index.html"><img src="{{ asset('assets/compiled/svg/logo-metinca.svg') }}" alt="logo-metinca"
                                    srcset=""></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20"
                                height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                        opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                aria-hidden="true" role="img" class="iconify iconify--mdi" width="20"
                                height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                </path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler  x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i
                                    class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                {{-- SIDEBAR MENU: Main Navigation --}}
                {{-- Fungsi: Menu utama untuk navigasi ke semua modul sistem --}}
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        {{-- Menu Item 1: Dashboard --}}
                        {{-- Fungsi: Halaman utama dengan overview KPI dan aktivitas terbaru --}}
                        <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }} ">
                            <a href="{{ route('dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        {{-- =====================================================
                            ADMIN ONLY MENUS - Hanya visible untuk user dengan role 'admin'
                            ===================================================== --}}
                        @if(Auth::check() && Auth::user()->isAdminOrManager())

                        {{-- Menu Item 2: Master Data --}}
                        {{-- Fungsi: Manajemen data karyawan, departemen, dan posisi kerja --}}
                        {{-- Submenu: Employee Data, Department & Line --}}
                        <li class="sidebar-item has-sub {{ request()->is('master-data*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-database-fill"></i>
                                <span>Master Data</span>
                            </a>
                            <ul class="submenu">
                                {{-- Submenu 1: Employee Data --}}
                                {{-- Isi: Daftar karyawan, NIK, nama, departemen, posisi, status --}}
                                <li class="submenu-item {{ request()->fullUrlIs('*master-data#karyawan*') ? 'active' : '' }}">
                                    <a href="{{ route('master-data') }}#karyawan" class="submenu-link">Employee</a>
                                </li>
                                {{-- Submenu 2: Department --}}
                                {{-- Isi: Daftar departemen dengan jumlah divisi dan karyawan --}}
                                <li class="submenu-item {{ request()->fullUrlIs('*master-data#departemen*') ? 'active' : '' }}">
                                    <a href="{{ route('master-data') }}#departemen" class="submenu-link">Department</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Menu Item 3: Evaluation & Exam --}}
                        {{-- Fungsi: Manajemen soal ujian dan tracking hasil ujian karyawan --}}
                        {{-- Submenu: Bank Soal, Sesi Ujian, Penilaian Hasil Ujian, Hasil Ujian --}}
                        <li class="sidebar-item has-sub {{ request()->is('evaluation-and-exam*') || request()->is('cbt/admin*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-clipboard-check-fill"></i>
                                <span>Assessments</span>
                            </a>
                            <ul class="submenu">
                                {{-- CBT: Bank Soal --}}
                                <li class="submenu-item {{ request()->is('cbt/admin/questions*') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.admin.questions.index') }}" class="submenu-link">Bank Soal</a>
                                </li>
                                {{-- CBT: Sesi Ujian / Penugasan --}}
                                <li class="submenu-item {{ request()->is('cbt/admin/sessions*') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.admin.sessions.index') }}" class="submenu-link">Sesi Ujian</a>
                                </li>
                                {{-- CBT: Verifikasi (Pending) --}}
                                <li class="submenu-item {{ request()->is('cbt/admin/sessions/pending*') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.admin.sessions.pending') }}" class="submenu-link">
                                        Penilaian Hasil Ujian
                                        @php
                                            $pendingCount = \App\Models\ExamSession::where('status', 'submitted')->count();
                                        @endphp
                                        @if($pendingCount > 0)
                                            <span class="badge bg-danger ms-auto">{{ $pendingCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                {{-- CBT: Matriks Kompetensi --}}
                                <li class="submenu-item {{ request()->is('cbt/admin/competency-matrix*') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.admin.competency-matrix') }}" class="submenu-link">Matriks Kompetensi</a>
                                </li>
                                @if(Auth::user()->isManager())
                                <li class="submenu-item {{ request()->is('cbt/admin/approval*') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.admin.sessions.pending-approval') }}" class="submenu-link">
                                        Persetujuan Level
                                        @php
                                            $pendingApprovalCount = \App\Models\ExamSession::where('status', 'verified_pass')
                                                ->where(function($q) { $q->where('manager_decision', 'pending')->orWhereNull('manager_decision'); })
                                                ->count();
                                        @endphp
                                        @if($pendingApprovalCount > 0)
                                            <span class="badge bg-warning ms-auto">{{ $pendingApprovalCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>

                        {{-- Menu: Manajemen User --}}
                        <li class="sidebar-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.index') }}" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Manajemen User</span>
                            </a>
                        </li>

                        @else

                        {{-- =====================================================
                            USER MENUS - Hanya untuk user dengan role 'user'
                            ===================================================== --}}

                        {{-- Menu Item 2: My Training --}}
                        {{-- Fungsi: Melihat training yang assigned untuk user tersebut --}}
                        <li class="sidebar-item {{ request()->routeIs('user.my-training') ? 'active' : '' }}">
                            <a href="{{ route('user.my-training') }}" class='sidebar-link'>
                                <i class="bi bi-book-half"></i>
                                <span>My Training</span>
                            </a>
                        </li>

                        {{-- Menu Item 3: CBT - Ujian Saya --}}
                        {{-- Fungsi: Ujian kompetensi yang ditugaskan dan hasil ujian --}}
                        <li class="sidebar-item has-sub {{ request()->is('cbt/*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-pencil-square"></i>
                                <span>Ujian Kompetensi</span>
                            </a>
                            <ul class="submenu">
                                {{-- Dashboard Ujian --}}
                                <li class="submenu-item {{ request()->routeIs('cbt.employee.dashboard') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.employee.dashboard') }}" class="submenu-link">
                                        Ujian Saya
                                        @if(Auth::user()->employee)
                                            @php
                                                $pendingExamCount = \App\Models\ExamSession::where('employee_nik', Auth::user()->employee->nik)
                                                    ->whereIn('status', ['assigned', 'started'])
                                                    ->count();
                                            @endphp
                                            @if($pendingExamCount > 0)
                                                <span class="badge bg-warning ms-auto">{{ $pendingExamCount }}</span>
                                            @endif
                                        @endif
                                    </a>
                                </li>
                                {{-- Kompetensi Saya --}}
                                <li class="submenu-item {{ request()->routeIs('cbt.employee.competencies') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.employee.competencies') }}" class="submenu-link">Kompetensi Saya</a>
                                </li>
                                {{-- Riwayat Ujian --}}
                                <li class="submenu-item {{ request()->routeIs('cbt.employee.history') ? 'active' : '' }}">
                                    <a href="{{ route('cbt.employee.history') }}" class="submenu-link">Riwayat Ujian</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Menu Item 4: Training History --}}
                        {{-- Fungsi: Melihat riwayat training dan assessment yang sudah selesai --}}
                        <li class="sidebar-item {{ request()->routeIs('user.training-history') ? 'active' : '' }}">
                            <a href="{{ route('user.training-history') }}" class='sidebar-link'>
                                <i class="bi bi-clock-history"></i>
                                <span>Training History</span>
                            </a>
                        </li>

                        {{-- Menu Item 5: My Profile --}}
                        {{-- Fungsi: Edit profil user, lihat informasi pribadi, dan change password --}}
                        <li class="sidebar-item {{ request()->routeIs('user.my-profile') ? 'active' : '' }}">
                            <a href="{{ route('user.my-profile') }}" class='sidebar-link'>
                                <i class="bi bi-person-fill"></i>
                                <span>My Profile</span>
                            </a>
                        </li>

                        @endif
                    </ul>
                </div>
                <!-- END SIDEBAR MENU -->
            </div>
        </div>
        <div id="main">
            {{-- ========================================
                 TOPBAR - Rebuilt from scratch
                 Clean structure, no Bootstrap dropdown dependency
            ======================================== --}}
            <header class="topbar">
                <div class="topbar-container">
                    {{-- Burger Button --}}
                    <button class="topbar-burger" id="sidebarToggle" type="button">
                        <i class="bi bi-justify fs-3"></i>
                    </button>

                    {{-- Right Side Items --}}
                    <div class="topbar-items">
                        {{-- Notification Bell --}}
                        @php
                            $pendingSessions = \App\Models\ExamSession::where('status', 'submitted')
                                ->with(['employee', 'exam'])
                                ->orderBy('submitted_at', 'desc')
                                ->limit(5)
                                ->get();
                            $pendingCount = $pendingSessions->count();
                        @endphp
                        
                        <div class="topbar-item" id="notificationWrapper">
                            <button class="topbar-btn" id="notificationBtn" type="button">
                                <i class="bi bi-bell fs-4"></i>
                                @if($pendingCount > 0)
                                    <span class="topbar-badge">{{ $pendingCount }}</span>
                                @endif
                            </button>
                            
                            <div class="topbar-dropdown" id="notificationDropdown">
                                <div class="topbar-dropdown-header">
                                    <h6 class="mb-0">Penilaian Hasil Ujian</h6>
                                </div>
                                <div class="topbar-dropdown-body">
                                    @forelse($pendingSessions as $session)
                                        <a href="{{ route('cbt.admin.sessions.show', $session) }}" class="topbar-dropdown-item">
                                            <div class="topbar-notification-icon">
                                                <i class="bi bi-clipboard-check"></i>
                                            </div>
                                            <div class="topbar-notification-content">
                                                <div class="topbar-notification-title">{{ $session->employee->name ?? 'Unknown' }}</div>
                                                <div class="topbar-notification-subtitle">{{ $session->exam->title ?? 'Exam' }}</div>
                                                <div class="topbar-notification-time">{{ $session->submitted_at?->diffForHumans() ?? 'Recently' }}</div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="topbar-dropdown-empty">
                                            <i class="bi bi-check-circle text-success fs-1"></i>
                                            <p>Semua ujian sudah diverifikasi</p>
                                        </div>
                                    @endforelse
                                </div>
                                @if($pendingCount > 0)
                                    <div class="topbar-dropdown-footer">
                                        <a href="{{ route('cbt.admin.sessions.pending') }}">
                                            <i class="bi bi-arrow-right-circle"></i> Lihat Semua
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- User Menu --}}
                        <div class="topbar-item" id="userMenuWrapper">
                            <button class="topbar-btn topbar-user-btn" id="userMenuBtn" type="button">
                                <div class="topbar-user-info">
                                    <div class="topbar-user-name">{{ auth()->user()->name }}</div>
                                    <div class="topbar-user-role">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Employee' }}</div>
                                </div>
                                <div class="topbar-user-avatar">
                                    @if(auth()->user()->employee && auth()->user()->employee->profile_photo_url)
                                        <img src="{{ asset('storage/' . auth()->user()->employee->profile_photo_url) }}" alt="{{ auth()->user()->name }}" loading="lazy">
                                    @elseif(auth()->user()->profile_photo_url)
                                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_url) }}" alt="{{ auth()->user()->name }}" loading="lazy">
                                    @else
                                        <img src="{{ asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar" loading="lazy">
                                    @endif
                                </div>
                            </button>
                            
                            <div class="topbar-dropdown topbar-dropdown-user" id="userMenuDropdown">
                                <div class="topbar-dropdown-header">
                                    <h6 class="mb-0">Halo, {{ auth()->user()->name }}!</h6>
                                </div>
                                <div class="topbar-dropdown-body">
                                    <a href="#" class="topbar-dropdown-item">
                                        <i class="bi bi-person me-2"></i> Profil Saya
                                    </a>
                                    <a href="#" class="topbar-dropdown-item">
                                        <i class="bi bi-gear me-2"></i> Pengaturan
                                    </a>
                                    <div class="topbar-dropdown-divider"></div>
                                    <form id="formLogout">
                                        @csrf
                                        <button type="submit" class="topbar-dropdown-item topbar-logout-btn">
                                            <i class="bi bi-box-arrow-left me-2"></i> Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            </header>

            <div class="main-content">
                @yield('content')
            </div>


            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2025 &copy; Sistem Informasi Universitas Darma Persada</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                            by <a href="si.unsada.ac.id">Your Name</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
     <!-- App JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Global: Disable "leave site" warning for auto-submit forms
        (function() {
            let isAutoSubmitting = false;
            
            // Global beforeunload handler
            window.addEventListener('beforeunload', function(e) {
                if (isAutoSubmitting) {
                    // Allow navigation without warning when auto-submitting
                    delete e['returnValue'];
                    return undefined;
                }
            }, true); // Use capture phase to ensure this runs first
            
            // Global helper function for auto-submit
            window.autoSubmitForm = function(form) {
                if (!form) return;
                isAutoSubmitting = true;
                setTimeout(() => {
                    if (form && typeof form.submit === 'function') {
                        form.submit();
                    }
                }, 100);
            };
        })();

        // ========================================
        // TOPBAR: Simple Vanilla JS - No Bootstrap
        // ========================================
        (function() {
            // Elements
            const sidebarToggle = document.getElementById('sidebarToggle');
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenuDropdown = document.getElementById('userMenuDropdown');
            
            // Toggle dropdown function
            function toggleDropdown(dropdown) {
                const isOpen = dropdown.classList.contains('show');
                
                // Close all dropdowns first
                document.querySelectorAll('.topbar-dropdown.show').forEach(d => {
                    d.classList.remove('show');
                });
                
                // Toggle current dropdown
                if (!isOpen) {
                    dropdown.classList.add('show');
                }
            }
            
            // Notification button click
            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleDropdown(notificationDropdown);
                    
                    // Hapus badge saat dibuka
                    const badge = notificationBtn.querySelector('.topbar-badge');
                    if (badge) {
                        badge.style.display = 'none';
                    }
                });
            }
            
            // User menu button click
            if (userMenuBtn && userMenuDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleDropdown(userMenuDropdown);
                });
            }
            
            // Sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const sidebar = document.getElementById('sidebar');
                    const mainContent = document.getElementById('main');
                    
                    if (sidebar) {
                        // Toggle sidebar state
                        if (sidebar.classList.contains('active')) {
                            sidebar.classList.remove('active');
                            sidebar.classList.add('inactive');
                        } else {
                            sidebar.classList.remove('inactive');
                            sidebar.classList.add('active');
                        }
                        
                        // Adjust main content width on desktop
                        if (mainContent && window.innerWidth >= 1200) {
                            if (sidebar.classList.contains('inactive')) {
                                mainContent.style.marginLeft = '0';
                            } else {
                                mainContent.style.marginLeft = '';
                            }
                        }
                    }
                });
            }
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.topbar-item')) {
                    document.querySelectorAll('.topbar-dropdown.show').forEach(d => {
                        d.classList.remove('show');
                    });
                }
            });
            
            // Close dropdowns on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.topbar-dropdown.show').forEach(d => {
                        d.classList.remove('show');
                    });
                }
            });
        })();

        document.getElementById('formLogout').addEventListener('submit', function(e){
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: 'Yakin ingin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                // Hanya logout jika user klik "Ya, Logout"
                if (result.isConfirmed) {
                    App.ajax('{{ route('logout') }}', 'POST', new FormData(form)).then(response => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Anda telah logout.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route('login') }}';
                        });
                        
                    }).catch(error => {
                        console.log(error);
                        App.error('Gagal Logout' || 'Terjadi kesalahan saat logout.');
                    });
                }
                // Jika klik "Batal" atau close, tidak terjadi apa-apa (logout dibatalkan)
            });
        });

    </script>
    @stack('scripts')
    <!-- Need: Apexcharts -->

</body>

</html>
