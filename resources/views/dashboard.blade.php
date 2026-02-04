@extends('layouts.dashboard')

@section('page-title', 'Dashboard')

@section('content')

<!-- Welcome Header -->
<div class="welcome-header">
    <div class="welcome-content">
        <div class="welcome-avatar">
            @if(auth()->user()->foto)
                <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="{{ auth()->user()->nama_lengkap }}">
            @else
                {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 2)) }}
            @endif
        </div>
        
        <div class="welcome-info">
            <h1 class="welcome-title">
                Assalamu'alaikum, {{ auth()->user()->name }} 👋
            </h1>
            <p class="welcome-subtitle">
                @php
                    $role = auth()->user()->getPrimaryRole();
                @endphp
                {{ $role->nama ?? 'User' }} | {{ now()->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ rand(150, 300) }}</div>
            <div class="stat-label">Total Santri</div>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ rand(120, 200) }}</div>
            <div class="stat-label">Santri Aktif</div>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">Rp {{ number_format(rand(50, 150) * 1000000, 0, ',', '.') }}</div>
            <div class="stat-label">Pembayaran Bulan Ini</div>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ rand(15, 30) }}</div>
            <div class="stat-label">Kelas Aktif</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3 class="section-title">Menu Cepat</h3>
    <div class="actions-grid">
        <a href="{{ route('pembayaran.index') }}" class="action-card">
            <div class="action-icon">💰</div>
            <div class="action-title">Pembayaran</div>
            <div class="action-desc">Kelola pembayaran santri</div>
        </a>

        <a href="{{ route('stats.santri') }}" class="action-card">
            <div class="action-icon">📊</div>
            <div class="action-title">Statistik</div>
            <div class="action-desc">Lihat statistik santri</div>
        </a>

        <a href="#" class="action-card">
            <div class="action-icon">📚</div>
            <div class="action-title">Akademik</div>
            <div class="action-desc">Kelola data akademik</div>
        </a>

        <a href="#" class="action-card">
            <div class="action-icon">👥</div>
            <div class="action-title">Santri</div>
            <div class="action-desc">Kelola data santri</div>
        </a>
    </div>
</div>

<style>
    .welcome-header {
        background: linear-gradient(135deg, #1a3a2e 0%, #0f2419 100%);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 4px 12px rgba(26, 58, 46, 0.2);
    }

    .welcome-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .welcome-avatar {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: rgba(212, 175, 55, 0.2);
        border: 3px solid #d4af37;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        color: #d4af37;
        overflow: hidden;
    }

    .welcome-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .welcome-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: white;
    }

    .welcome-subtitle {
        font-size: 0.9375rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon svg {
        width: 32px;
        height: 32px;
    }

    .stat-primary .stat-icon {
        background: #dbeafe;
        color: #2563eb;
    }

    .stat-success .stat-icon {
        background: #d1fae5;
        color: #10b981;
    }

    .stat-warning .stat-icon {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-info .stat-icon {
        background: #e0e7ff;
        color: #6366f1;
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.25rem;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
    }

    .action-card {
        background: white;
        border-radius: 12px;
        padding: 1.75rem;
        text-align: center;
        text-decoration: none;
        border: 2px solid #e5e7eb;
        transition: all 0.3s;
    }

    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #1a3a2e;
    }

    .action-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }

    .action-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.5rem;
    }

    .action-desc {
        font-size: 0.875rem;
        color: #64748b;
    }

    @media (max-width: 768px) {
        .welcome-content {
            flex-direction: column;
            text-align: center;
        }

        .welcome-title {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection