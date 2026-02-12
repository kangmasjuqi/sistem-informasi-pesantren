        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/" class="sidebar-logo">
                    <span class="sidebar-logo-icon">P</span>
                    <span>SI Pesantren</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Menu</div>
                    
                    {{-- Dashboard - All Roles --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN', 'ADMIN', 'KEPSEK', 'PENGAJAR', 'WALIKELAS', 'STAFF_TU', 'BENDAHARA']))
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    {{-- Santri Dashboard - For Santri Only --}}
                    @if(auth()->user()->hasRole('SANTRI'))
                    <a href="{{ route('santri.dashboard') }}" class="nav-link {{ request()->routeIs('santri.dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    {{-- Wali Dashboard - For Wali Santri Only --}}
                    @if(auth()->user()->hasRole('WALI'))
                    <a href="{{ route('wali.dashboard') }}" class="nav-link {{ request()->routeIs('wali.dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    {{-- Statistik Santri - Admin, Kepsek, Staff TU --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN', 'ADMIN', 'KEPSEK', 'STAFF_TU']))
                    <a href="{{ route('stats.santri') }}" class="nav-link {{ request()->routeIs('stats.santri') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Statistik Santri</span>
                    </a>
                    @endif

                    {{-- Pembayaran - Admin, Bendahara, Staff TU, Kepsek --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN', 'ADMIN', 'BENDAHARA', 'STAFF_TU', 'KEPSEK']))
                    <a href="{{ route('pembayaran.index') }}" class="nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V9m0 0h4m-4 0l-4-4"/>
                        </svg>
                        <span>Pembayaran</span>
                    </a>
                    @endif

                    {{-- Tahun Ajaran - Admin, Bendahara, Staff TU, Kepsek --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN', 'ADMIN', 'BENDAHARA', 'STAFF_TU', 'KEPSEK']))
                    <a href="{{ route('tahun-ajaran.index') }}" class="nav-link {{ request()->routeIs('tahun-ajaran.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V9m0 0h4m-4 0l-4-4"/>
                        </svg>
                        <span>Tahun Ajaran</span>
                    </a>
                    @endif

                    {{-- User Management - SuperAdmin & Admin Only --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN', 'ADMIN']))
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>User</span>
                    </a>
                    <a href="/pelajaran" class="nav-link {{ request()->is('pelajaran*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-7-3h14"/>
                        </svg>
                        <span>Pelajaran</span>
                    </a>
                    @endif
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Akun</div>
                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                        @csrf
                        <button type="submit" class="nav-link nav-link-logout">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>

                <style>
                    .nav-link-logout {
                        background: none;
                        border: none;
                        width: 100%;
                        text-align: left;
                        cursor: pointer;
                        font-family: inherit;
                        font-size: inherit;
                        transition: all 0.2s;
                    }

                    .nav-link-logout:hover {
                        background: rgba(239, 68, 68, 0.1);
                        color: #dc2626;
                    }

                    .nav-link-logout:hover .nav-icon {
                        color: #dc2626;
                    }

                    .nav-section + .nav-section {
                        margin-top: 2rem;
                        padding-top: 2rem;
                        border-top: 1px solid rgba(255, 255, 255, 0.1);
                    }
                </style>

                <script>
                    // Optional: Add confirmation dialog
                    document.getElementById('logoutForm')?.addEventListener('submit', function(e) {
                        if (!confirm('Yakin ingin logout?')) {
                            e.preventDefault();
                        }
                    });
                </script>         

<!--                
                <div class="nav-section">
                    <div class="nav-section-title">Akademik</div>
                    <a href="#kelas" class="nav-link">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Kelas</span>
                    </a>
                    <a href="#kehadiran" class="nav-link">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span>Kehadiran</span>
                    </a>
                    <a href="#nilai" class="nav-link">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Nilai</span>
                    </a>
                    <a href="#rapor" class="nav-link">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Rapor</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Fasilitas</div>
                    <a href="#kamar" class="nav-link">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Kamar</span>
                    </a>
                    <a href="#perizinan" class="nav-link">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <span>Perizinan</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Keuangan</div>
                    <a href="#pembayaran" class="nav-link">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Pembayaran</span>
                    </a>
                </div>

-->
            </nav>
        </aside>