<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Metinca Training</title>

    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/iconly.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-overrides.css') }}">
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
                            <a href="{{ route('dashboard') }}"><img src="{{ asset('assets/compiled/svg/logo-metinca.svg') }}" alt="logo-metinca" srcset=""></a>
                        </div>
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
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
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark"
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
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR MENU: User Navigation --}}
                {{-- Fungsi: Menu sederhana untuk user karyawan --}}
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu Saya</li>

                        {{-- Menu Item 1: Dashboard --}}
                        <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        {{-- Menu Item 2: Pelatihan dengan submenu --}}
                        <li class="sidebar-item has-sub {{ request()->is('my-training*') || request()->is('training-history*') || request()->is('my-competencies*') ? 'active' : '' }}">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-book-fill"></i>
                                <span>Pelatihan Saya</span>
                            </a>
                            <ul class="submenu">
                                {{-- Submenu 1: Daftar Ujian --}}
                                <li class="submenu-item {{ request()->is('my-training*') ? 'active' : '' }}">
                                    <a href="{{ route('user.my-training') }}" class="submenu-link">Daftar Ujian</a>
                                </li>
                                {{-- Submenu 2: Riwayat Ujian --}}
                                <li class="submenu-item {{ request()->is('training-history*') ? 'active' : '' }}">
                                    <a href="{{ route('user.training-history') }}" class="submenu-link">Riwayat Ujian</a>
                                </li>
                                {{-- Submenu 3: Kompetensi Saya --}}
                                <li class="submenu-item {{ request()->is('my-competencies*') ? 'active' : '' }}">
                                    <a href="{{ route('user.my-competencies') }}" class="submenu-link">Kompetensi Saya</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Menu Item 3: My Profile --}}
                        <li class="sidebar-item {{ request()->is('my-profile*') ? 'active' : '' }}">
                            <a href="{{ route('user.my-profile') }}" class='sidebar-link'>
                                <i class="bi bi-person-fill"></i>
                                <span>Profil Saya</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="main">
            {{-- ========================================
                 TOPBAR - Struktur sama dengan layouts/app.blade.php
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
                        <div class="topbar-item" id="notificationWrapper">
                            <button class="topbar-btn" id="notificationBtn" type="button">
                                <i class="bi bi-bell fs-4"></i>
                            </button>

                            <div class="topbar-dropdown" id="notificationDropdown">
                                <div class="topbar-dropdown-header">
                                    <h6 class="mb-0">Notifikasi</h6>
                                </div>
                                <div class="topbar-dropdown-body">
                                    <div class="topbar-dropdown-empty">
                                        <i class="bi bi-bell-slash text-muted fs-1"></i>
                                        <p>Belum ada notifikasi</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- User Menu --}}
                        <div class="topbar-item" id="userMenuWrapper">
                            <button class="topbar-btn topbar-user-btn" id="userMenuBtn" type="button">
                                <div class="topbar-user-info">
                                    <div class="topbar-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                                    <div class="topbar-user-role">Karyawan</div>
                                </div>
                                <div class="topbar-user-avatar">
                                    <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/compiled/jpg/1.jpg') }}" alt="Avatar" loading="lazy">
                                </div>
                            </button>

                            <div class="topbar-dropdown topbar-dropdown-user" id="userMenuDropdown">
                                <a href="{{ route('user.my-profile') }}" class="topbar-dropdown-item">
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
            </header>

            <div class="main-content">
                @yield('content')
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2026 &copy; PT Metinca</p>
                    </div>
                    <div class="float-end">
                        <p>Sistem Manajemen Kompetensi &amp; Pelatihan</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>window.SWAL_BTN = { danger: '#dc3545', success: '#198754', cancel: '#6c757d' };</script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    @auth
    @if(Auth::user()->employee)
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', {
            wsHost: '{{ env("REVERB_HOST", "localhost") }}',
            wsPort: {{ env("REVERB_PORT", 8080) }},
            forceTLS: false,
            encrypted: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        const employeeChannel = pusher.subscribe('private-employee.{{ Auth::user()->employee->nik }}');
        employeeChannel.bind('App\\Events\\SessionStatusUpdated', function(data) {
            if (data.action === 'verified' || data.action === 'approved' || data.action === 'rejected') {
                const toastMsg = data.action === 'approved' 
                    ? 'Level Anda telah DISETUJUI Manager!' 
                    : data.action === 'rejected'
                    ? 'Level Anda ditolak Manager'
                    : 'Hasil ujian "' + data.exam_title + '" telah diverifikasi!';
                App.toast(data.action === 'approved' ? 'success' : 'info', toastMsg);
            }
        });
    });
    </script>
    @endif
    @endauth

    <script>
        // ========================================
        // TOPBAR: Simple Vanilla JS - Sama dengan layouts/app.blade.php
        // ========================================
        (function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const notificationBtn = document.getElementById('notificationBtn');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenuDropdown = document.getElementById('userMenuDropdown');

            function toggleDropdown(dropdown) {
                const isOpen = dropdown.classList.contains('show');

                document.querySelectorAll('.topbar-dropdown.show').forEach(d => {
                    d.classList.remove('show');
                });

                if (!isOpen) {
                    dropdown.classList.add('show');
                }
            }

            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleDropdown(notificationDropdown);
                });
            }

            if (userMenuBtn && userMenuDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleDropdown(userMenuDropdown);
                });
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const sidebar = document.getElementById('sidebar');
                    const mainContent = document.getElementById('main');

                    if (sidebar) {
                        sidebar.classList.toggle('active');
                        sidebar.classList.toggle('inactive');

                        if (window.innerWidth >= 1200 && mainContent) {
                            mainContent.style.marginLeft =
                                sidebar.classList.contains('inactive') ? '0' : '';
                        }

                        if (window.innerWidth < 1200) {
                            let backdrop = document.querySelector('.sidebar-backdrop');
                            if (!backdrop) {
                                backdrop = document.createElement('div');
                                backdrop.className = 'sidebar-backdrop';
                                backdrop.onclick = function() {
                                    sidebar.classList.remove('active');
                                    sidebar.classList.add('inactive');
                                    backdrop.remove();
                                };
                                document.body.appendChild(backdrop);
                            } else {
                                backdrop.remove();
                            }
                        }
                    }
                });
            }

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.topbar-item')) {
                    document.querySelectorAll('.topbar-dropdown.show').forEach(d => {
                        d.classList.remove('show');
                    });
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.topbar-dropdown.show').forEach(d => {
                        d.classList.remove('show');
                    });
                }
            });
        })();

        const formLogout = document.getElementById('formLogout');
        if (formLogout) {
            formLogout.addEventListener('submit', function(e){
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
                    if (result.isConfirmed) {
                        App.ajax('{{ route('logout') }}', 'POST', new FormData(form)).then(response => {
                            window.location.href = '{{ route('login') }}';
                        }).catch(error => {
                            console.log(error);
                            App.error('Gagal Logout' || 'Terjadi kesalahan saat logout.');
                        });
                    }
                });
            });
        }
    </script>
    @stack('scripts')
</body>

</html>
