@extends('layouts.dashboard')

@section('page-title', 'Dashboard Wali Santri')

@section('content')

<!-- Welcome Header -->
<div class="wali-header">
    <div class="wali-content">
        <div class="wali-avatar">
            @if(auth()->user()->foto)
                <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="{{ auth()->user()->nama_lengkap }}">
            @else
                {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 2)) }}
            @endif
        </div>
        
        <div class="wali-info">
            <h1 class="wali-title">
                Assalamu'alaikum, {{ auth()->user()->name }} 👨‍👩‍👧‍👦
            </h1>
            <p class="wali-subtitle">
                Wali Santri | {{ now()->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>
</div>

<!-- Children List -->
<div class="children-section">
    <h3 class="section-title">👨‍👩‍👧 Putra/Putri Anda</h3>
    
    <div class="children-grid">
        <!-- Child 1 -->
        <div class="child-card">
            <div class="child-header">
                <div class="child-avatar">AS</div>
                <div class="child-info">
                    <h4 class="child-name">Ahmad Sukri</h4>
                    <p class="child-class">Kelas X IPA 1 • NIS: 2025001</p>
                </div>
                <span class="badge badge-success">AKTIF</span>
            </div>
            
            <div class="child-body">
                <div class="child-stats">
                    <div class="stat-item">
                        <div class="stat-label">Kehadiran</div>
                        <div class="stat-value text-success">95%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata</div>
                        <div class="stat-value text-primary">85.5</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Ranking</div>
                        <div class="stat-value text-warning">5 / 30</div>
                    </div>
                </div>

                <div class="payment-info">
                    <div class="payment-row">
                        <span>SPP Februari 2026</span>
                        <span class="badge badge-success">LUNAS</span>
                    </div>
                    <div class="payment-row">
                        <span>SPP Maret 2026</span>
                        <span class="badge badge-danger">BELUM LUNAS</span>
                    </div>
                </div>

                <a href="#" class="btn-detail">Lihat Detail →</a>
            </div>
        </div>

        <!-- Child 2 -->
        <div class="child-card">
            <div class="child-header">
                <div class="child-avatar">SN</div>
                <div class="child-info">
                    <h4 class="child-name">Siti Nurhaliza</h4>
                    <p class="child-class">Kelas VIII A • NIS: 2025024</p>
                </div>
                <span class="badge badge-success">AKTIF</span>
            </div>
            
            <div class="child-body">
                <div class="child-stats">
                    <div class="stat-item">
                        <div class="stat-label">Kehadiran</div>
                        <div class="stat-value text-success">98%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Rata-rata</div>
                        <div class="stat-value text-primary">90.2</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Ranking</div>
                        <div class="stat-value text-warning">2 / 28</div>
                    </div>
                </div>

                <div class="payment-info">
                    <div class="payment-row">
                        <span>SPP Februari 2026</span>
                        <span class="badge badge-success">LUNAS</span>
                    </div>
                    <div class="payment-row">
                        <span>SPP Maret 2026</span>
                        <span class="badge badge-success">LUNAS</span>
                    </div>
                </div>

                <a href="#" class="btn-detail">Lihat Detail →</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-access">
    <h3 class="section-title">📋 Akses Cepat</h3>
    
    <div class="access-grid">
        <a href="#" class="access-card">
            <div class="access-icon">💰</div>
            <div class="access-content">
                <h4>Riwayat Pembayaran</h4>
                <p>Lihat semua transaksi pembayaran</p>
            </div>
        </a>

        <a href="#" class="access-card">
            <div class="access-icon">📊</div>
            <div class="access-content">
                <h4>Laporan Akademik</h4>
                <p>Nilai, rapor, dan prestasi</p>
            </div>
        </a>

        <a href="#" class="access-card">
            <div class="access-icon">📅</div>
            <div class="access-content">
                <h4>Absensi</h4>
                <p>Rekap kehadiran putra/putri</p>
            </div>
        </a>

        <a href="#" class="access-card">
            <div class="access-icon">📞</div>
            <div class="access-content">
                <h4>Hubungi Sekolah</h4>
                <p>Kontak wali kelas & admin</p>
            </div>
        </a>
    </div>
</div>

<!-- Notifications -->
<div class="notifications-section">
    <h3 class="section-title">🔔 Pemberitahuan Terbaru</h3>
    
    <div class="notification-list">
        <div class="notification-item">
            <div class="notif-icon notif-warning">⚠️</div>
            <div class="notif-content">
                <h4>Pembayaran SPP Maret</h4>
                <p>Pembayaran SPP bulan Maret untuk Ahmad Sukri jatuh tempo tanggal 10 Maret 2026</p>
                <span class="notif-time">2 hari yang lalu</span>
            </div>
        </div>

        <div class="notification-item">
            <div class="notif-icon notif-success">✅</div>
            <div class="notif-content">
                <h4>Prestasi Baru</h4>
                <p>Siti Nurhaliza meraih Juara 1 Lomba Tahfidz tingkat Kabupaten</p>
                <span class="notif-time">5 hari yang lalu</span>
            </div>
        </div>

        <div class="notification-item">
            <div class="notif-icon notif-info">ℹ️</div>
            <div class="notif-content">
                <h4>Rapat Wali Murid</h4>
                <p>Rapat wali murid akan dilaksanakan Sabtu, 15 Maret 2026 pukul 09.00 WIB</p>
                <span class="notif-time">1 minggu yang lalu</span>
            </div>
        </div>
    </div>
</div>

<style>
    .wali-header {
        background: linear-gradient(135deg, #1a3a2e 0%, #0f2419 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .wali-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .wali-avatar {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.2);
        border: 3px solid rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        overflow: hidden;
    }

    .wali-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .wali-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .wali-subtitle {
        font-size: 0.9375rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.25rem;
    }

    .children-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .child-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
    }

    .child-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .child-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .child-avatar {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1a3a2e 0%, #0f2419 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .child-info {
        flex: 1;
    }

    .child-name {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.25rem 0;
    }

    .child-class {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .child-body {
        padding: 1.5rem;
    }

    .child-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .stat-item {
        text-align: center;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .text-success { color: #10b981; }
    .text-primary { color: #2563eb; }
    .text-warning { color: #f59e0b; }

    .payment-info {
        margin-bottom: 1.25rem;
    }

    .payment-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .btn-detail {
        display: inline-block;
        color: #10b981;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }

    .btn-detail:hover {
        color: #059669;
    }

    .access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .access-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid #e5e7eb;
        text-decoration: none;
        transition: all 0.3s;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .access-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #10b981;
    }

    .access-icon {
        font-size: 2.5rem;
    }

    .access-content h4 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.5rem 0;
    }

    .access-content p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .notification-list {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    .notification-item {
        display: flex;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background: #f9fafb;
    }

    .notif-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notif-warning {
        background: #fef3c7;
    }

    .notif-success {
        background: #d1fae5;
    }

    .notif-info {
        background: #dbeafe;
    }

    .notif-content h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.5rem 0;
    }

    .notif-content p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0 0 0.5rem 0;
        line-height: 1.5;
    }

    .notif-time {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    @media (max-width: 768px) {
        .wali-content {
            flex-direction: column;
            text-align: center;
        }

        .children-grid {
            grid-template-columns: 1fr;
        }

        .child-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection