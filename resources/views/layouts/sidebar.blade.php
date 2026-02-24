        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/" class="sidebar-logo">
                    <span class="sidebar-logo-icon">P</span>
                    <span>SI Pesantren</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">

                {{-- ============================================================
                    SIDEBAR NAVIGATION
                    Role legend:
                    SUPERADMIN  – Full system access
                    ADMIN       – General pesantren admin
                    KEPSEK      – Kepala Sekolah / Mudir
                    PENGAJAR    – Ustadz / Ustadzah
                    WALIKELAS   – Wali Kelas (homeroom teacher)
                    STAFF_TU    – Staff Tata Usaha
                    BENDAHARA   – Bendahara keuangan
                    SANTRI      – Student
                    WALI        – Wali Santri (parent/guardian)
                    ============================================================ --}}

                {{-- ================= AKADEMIK ================= --}}
                <div class="nav-section">
                    <div class="nav-section-title">Akademik</div>

                    {{-- Dashboard — all staff roles --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','PENGAJAR','WALIKELAS','STAFF_TU','BENDAHARA']))
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    {{-- Dashboard — santri portal --}}
                    @if(auth()->user()->hasRole('SANTRI'))
                    <a href="{{ route('santri.dashboard') }}" class="nav-link {{ request()->routeIs('santri.dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    {{-- Dashboard — wali santri portal --}}
                    @if(auth()->user()->hasRole('WALI'))
                    <a href="{{ route('wali.dashboard') }}" class="nav-link {{ request()->routeIs('wali.dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    {{-- Statistik Santri — management & admin layer --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','STAFF_TU']))
                    <a href="{{ route('stats.santri') }}" class="nav-link {{ request()->routeIs('stats.santri') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Statistik Santri</span>
                    </a>
                    @endif

                    {{-- Santri — student registry; all staff view, admin layer manages --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','PENGAJAR','WALIKELAS','STAFF_TU','BENDAHARA']))
                    <a href="{{ route('santri.index') }}" class="nav-link {{ request()->routeIs('santri.index') || request()->routeIs('santri.show') || request()->routeIs('santri.store') || request()->routeIs('santri.update') || request()->routeIs('santri.destroy') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Santri</span>
                    </a>
                    @endif

                    {{-- Wali Santri — guardian data; WALIKELAS needs contact access --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','WALIKELAS','STAFF_TU']))
                    <a href="{{ route('wali-santri.index') }}" class="nav-link {{ request()->routeIs('wali-santri.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Wali Santri</span>
                    </a>
                    @endif

                    {{-- Tahun Ajaran — academic calendar, visible to teaching staff too --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','PENGAJAR','WALIKELAS']))
                    <a href="{{ route('tahun-ajaran.index') }}" class="nav-link {{ request()->routeIs('tahun-ajaran.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Tahun Ajaran</span>
                    </a>
                    @endif

                    {{-- Pelajaran — teaching staff need to see & manage their subjects --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','PENGAJAR','WALIKELAS']))
                    <a href="{{ route('pelajaran.index') }}" class="nav-link {{ request()->routeIs('pelajaran.*') || request()->is('pelajaran*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-7-3h14"/>
                        </svg>
                        <span>Pelajaran</span>
                    </a>
                    @endif

                </div>

                {{-- ================= OPERASIONAL ================= --}}
                <div class="nav-section">
                    <div class="nav-section-title">Operasional</div>

                    {{-- Pembayaran — finance & admin roles; STAFF_TU assists with receipts --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','STAFF_TU','BENDAHARA']))
                    <a href="{{ route('pembayaran.index') }}" class="nav-link {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <span>Pembayaran</span>
                    </a>
                    @endif

                    {{-- Perizinan — leave/permission approval handled by admin & homeroom --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','WALIKELAS']))
                    <a href="{{ route('perizinan.index') }}" class="nav-link {{ request()->routeIs('perizinan.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Perizinan</span>
                    </a>
                    @endif

                    {{-- Pengajar — teacher HR records; same admin layer as Inventaris/Kamar --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','STAFF_TU']))
                    <a href="{{ route('pengajar.index') }}" class="nav-link {{ request()->routeIs('pengajar.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Pengajar</span>
                    </a>
                    @endif

                    {{-- Inventaris — physical asset management, STAFF_TU is the custodian --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','STAFF_TU']))
                    <a href="{{ route('inventaris.index') }}" class="nav-link {{ request()->routeIs('inventaris.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span>Inventaris</span>
                    </a>
                    @endif

                    {{-- Kamar — dormitory room management, STAFF_TU handles boarding admin --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','STAFF_TU']))
                    <a href="{{ route('kamar.index') }}" class="nav-link {{ request()->routeIs('kamar.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Kamar</span>
                    </a>
                    @endif

                    {{-- Penghuni Kamar — student room assignments, same as Kamar --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','STAFF_TU']))
                    <a href="{{ route('penghuni-kamar.index') }}" class="nav-link {{ request()->routeIs('penghuni-kamar.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Penghuni Kamar</span>
                    </a>
                    @endif

                    {{-- Users — account management is strictly SA + Admin --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN']))
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>User</span>
                    </a>
                    @endif

                </div>

                {{-- ================= MASTER ================= --}}
                <div class="nav-section">
                    <div class="nav-section-title">Master</div>

                    {{-- Komponen Nilai — grade weights configured by teaching staff --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK','PENGAJAR','WALIKELAS']))
                    <a href="{{ route('komponen-nilai.index') }}" class="nav-link {{ request()->routeIs('komponen-nilai.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Komponen Nilai</span>
                    </a>
                    @endif

                    {{-- Jenis Pembayaran — KEPSEK oversees fee structures --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK']))
                    <a href="{{ route('jenis-pembayaran.index') }}" class="nav-link {{ request()->routeIs('jenis-pembayaran.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Jenis Pembayaran</span>
                    </a>
                    @endif

                    {{-- Kategori Inventaris — KEPSEK oversees asset taxonomy --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK']))
                    <a href="{{ route('kategori-inventaris.index') }}" class="nav-link {{ request()->routeIs('kategori-inventaris.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span>Kategori Inventaris</span>
                    </a>
                    @endif

                    {{-- Gedung — KEPSEK oversees facility master data --}}
                    @if(auth()->user()->hasRole(['SUPERADMIN','ADMIN','KEPSEK']))
                    <a href="{{ route('gedung.index') }}" class="nav-link {{ request()->routeIs('gedung.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Gedung</span>
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