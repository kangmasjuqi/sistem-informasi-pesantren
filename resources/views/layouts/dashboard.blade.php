<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Dashboard') - Pesantren Management</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Extra CSS -->
    @yield('extra-css')


    <style>
        :root {
            --color-primary: #1a3a2e;
            --color-secondary: #4a7c59;
            --color-accent: #d4af37;
            --color-bg: #fafaf9;
            --color-surface: #ffffff;
            --color-text: #1f2937;
            --color-text-light: #6b7280;
            --color-border: #e5e7eb;
            --color-success: #059669;
            --color-warning: #f59e0b;
            --color-danger: #dc2626;
            --color-info: #0284c7;
            
            --sidebar-width: 280px;
            --header-height: 70px;
            
            --font-display: 'Crimson Pro', serif;
            --font-body: 'DM Sans', sans-serif;
            
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: var(--color-bg);
            color: var(--color-text);
            line-height: 1.6;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--color-primary) 0%, #0f2419 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-logo-icon {
            width: 40px;
            height: 40px;
            background: var(--color-accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--color-primary);
            font-size: 1.25rem;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.5);
            padding: 0 1.5rem;
            margin-bottom: 0.75rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
            position: relative;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .nav-link.active {
            background: rgba(212, 175, 55, 0.15);
            color: var(--color-accent);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--color-accent);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .content-header {
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            padding: 1.5rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .content-body {
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Cards */
        .card {
            background: var(--color-surface);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--color-border);
            background: linear-gradient(to right, rgba(26, 58, 46, 0.02), transparent);
        }

        .card-title {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Tabs */
        .tabs {
            border-bottom: 2px solid var(--color-border);
            margin-bottom: 2rem;
        }

        .tab-list {
            display: flex;
            gap: 2rem;
            list-style: none;
            padding: 0 1rem;
            overflow-x: auto;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 1rem 0.5rem;
            font-family: var(--font-body);
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-text-light);
            cursor: pointer;
            position: relative;
            white-space: nowrap;
            transition: color 0.2s ease;
        }

        .tab-button:hover {
            color: var(--color-text);
        }

        .tab-button.active {
            color: var(--color-primary);
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--color-accent);
            border-radius: 2px 2px 0 0;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            margin: -1.5rem;
            padding: 1.5rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9375rem;
        }

        .data-table thead {
            background: var(--color-bg);
        }

        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--color-text);
            border-bottom: 2px solid var(--color-border);
            white-space: nowrap;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--color-border);
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background: rgba(26, 58, 46, 0.02);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .badge-success { background: rgba(5, 150, 105, 0.1); color: var(--color-success); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--color-warning); }
        .badge-danger { background: rgba(220, 38, 38, 0.1); color: var(--color-danger); }
        .badge-info { background: rgba(2, 132, 199, 0.1); color: var(--color-info); }
        .badge-default { background: rgba(107, 114, 128, 0.1); color: var(--color-text-light); }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--color-surface);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border-left: 4px solid var(--color-accent);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--color-text-light);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .stat-description {
            font-size: 0.8125rem;
            color: var(--color-text-light);
            margin-top: 0.5rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .info-label {
            font-size: 0.875rem;
            color: var(--color-text-light);
            font-weight: 500;
        }

        .info-value {
            font-size: 1rem;
            color: var(--color-text);
            font-weight: 500;
        }

        /* Profile Header */
        .profile-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .profile-content {
            display: flex;
            gap: 2rem;
            align-items: start;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            border: 4px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .profile-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .profile-meta-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .profile-meta-label {
            font-size: 0.75rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .profile-meta-value {
            font-size: 1rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-body {
                padding: 1rem;
            }

            .profile-content {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .tab-list {
                gap: 1rem;
            }
        }

        /* Utilities */
        .text-muted { color: var(--color-text-light); }
        .text-success { color: var(--color-success); }
        .text-warning { color: var(--color-warning); }
        .text-danger { color: var(--color-danger); }
        .text-info { color: var(--color-info); }
        
        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-container">

        @include('layouts/sidebar')

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <div class="header-content">
                    <h1 class="page-title">@yield('page-title', 'Dashboard Santri')</h1>
                    <div class="header-actions">
                        @yield('header-actions')
                    </div>
                </div>
            </header>

            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tabs
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-tab');

                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    const targetContent = document.getElementById(targetId);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });

            // Handle hash navigation for sidebar links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href.startsWith('#')) {
                        e.preventDefault();
                        
                        // Update active state
                        navLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');

                        // Find and activate corresponding tab
                        const targetTab = href.substring(1);
                        const tabButton = document.querySelector(`[data-tab="${targetTab}"]`);
                        if (tabButton) {
                            tabButton.click();
                        }

                        // Smooth scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Extra JS -->
    @yield('extra-js')

    @stack('scripts')
</body>
</html>