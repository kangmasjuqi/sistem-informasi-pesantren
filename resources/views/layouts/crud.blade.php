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
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Extra CSS -->
    @yield('extra-css')

    <style>

        /* ============================================================
        CENTRALIZED STYLESHEET
        Merged from: crud layout, user, tahun ajaran, pembayaran,
        mata pelajaran
        ============================================================ */

        /* Google Fonts (add to <head> if not already present)
        Crimson Pro + DM Sans for layout, Inter for content pages */

        :root {
            /* Brand Colors */
            --primary-color: #1a3a2e;
            --primary-hover: #265443;
            --color-primary: #1a3a2e;
            --color-secondary: #4a7c59;
            --color-accent: #d4af37;

            /* Semantic Colors */
            --color-success: #059669;
            --color-warning: #f59e0b;
            --color-danger: #dc2626;
            --color-info: #0284c7;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;

            /* Surfaces & Text */
            --color-bg: #fafaf9;
            --color-surface: #ffffff;
            --color-text: #1f2937;
            --color-text-light: #6b7280;
            --color-border: #e5e7eb;
            --border-color: #e5e7eb;
            --text-muted: #6b7280;
            --bg-light: #f9fafb;

            /* Layout */
            --sidebar-width: 280px;
            --header-height: 70px;

            /* Typography */
            --font-display: 'Crimson Pro', serif;
            --font-body: 'DM Sans', sans-serif;

            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        /* ============================================================
        RESET & BASE
        ============================================================ */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, var(--font-body), sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        /* ============================================================
        LAYOUT
        ============================================================ */
        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        /* ============================================================
        SIDEBAR
        ============================================================ */
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

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }

        /* ============================================================
        CONTENT HEADER
        ============================================================ */
        .content-header {
            background: var(--color-surface);
            border-bottom: 1px solid var(--border-color);
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

        @media (max-width: 768px) {
            .content-body {
                padding: 1rem;
            }
        }

        /* ============================================================
        PAGE HEADER (inline, non-sticky variant)
        ============================================================ */
        .page-header {
            background: white;
            padding: 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.025em;
        }

        .page-header p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.875rem;
        }

        /* ============================================================
        CARDS
        ============================================================ */
        .card {
            background: var(--color-surface);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            background: white;
        }

        .card-title {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-primary);
            margin: 0;
        }

        .card-body {
            padding: 2rem;
        }

        /* Section card variant */
        .section-card {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .section-header {
            padding: 1rem 1.5rem;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-body {
            padding: 1.5rem;
            background: white;
        }

        /* ============================================================
        TABS
        ============================================================ */
        .tabs {
            border-bottom: 2px solid var(--border-color);
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
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-muted);
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

        @media (max-width: 768px) {
            .tab-list {
                gap: 1rem;
            }
        }

        /* ============================================================
        TABLES
        ============================================================ */
        .table-responsive {
            overflow-x: auto;
            margin: -1.5rem;
            padding: 1.5rem;
        }

        .data-table,
        table.dataTable {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
        }

        .data-table thead,
        table.dataTable thead {
            background: var(--bg-light);
        }

        .data-table th,
        table.dataTable thead th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #374151;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }

        .data-table td,
        table.dataTable tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .data-table tbody tr,
        table.dataTable tbody tr {
            transition: background 0.2s;
        }

        .data-table tbody tr:hover,
        table.dataTable tbody tr:hover {
            background: var(--bg-light);
        }

        /* ============================================================
        STATS GRID
        ============================================================ */
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
            color: var(--text-muted);
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
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ============================================================
        INFO GRID
        ============================================================ */
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
            color: var(--text-muted);
            font-weight: 500;
        }

        .info-value {
            font-size: 1rem;
            color: var(--color-text);
            font-weight: 500;
        }

        /* ============================================================
        PROFILE HEADER
        ============================================================ */
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

        @media (max-width: 768px) {
            .profile-content {
                flex-direction: column;
            }
        }

        /* ============================================================
        FILTERS
        ============================================================ */
        .filters-section {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        /* ============================================================
        FORMS
        ============================================================ */
        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-label .required {
            color: var(--danger-color);
            margin-left: 2px;
        }

        .form-control,
        .form-select {
            width: 100%;
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 58, 46, 0.1);
            outline: none;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--danger-color) !important;
        }

        .form-text {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.85rem;
            color: var(--danger-color);
            margin-top: 0.25rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
            user-select: none;
        }

        /* ============================================================
        BUTTONS
        ============================================================ */
        .btn {
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: white;
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-warning {
            background: var(--warning-color);
            color: white;
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary-color);
            border: 1.5px solid var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ============================================================
        BADGES
        ============================================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            line-height: 1.5;
        }

        /* Form label badges */
        .badge-required  { background: #fee2e2; color: #dc2626; font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 4px; }
        .badge-optional  { background: #e0e7ff; color: #4f46e5; font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 4px; }
        .badge-info      { background: #dbeafe; color: #2563eb; font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 4px; }
        .badge-auto      { background: #dbeafe; color: #2563eb; font-size: 0.625rem; padding: 0.125rem 0.5rem; border-radius: 4px; }

        /* Semantic color badges */
        .badge-success   { background: rgba(5, 150, 105, 0.1); color: var(--color-success); }
        .badge-warning   { background: rgba(245, 158, 11, 0.1); color: var(--color-warning); }
        .badge-danger    { background: rgba(220, 38, 38, 0.1); color: var(--color-danger); }
        .badge-default   { background: rgba(107, 114, 128, 0.1); color: var(--text-muted); }

        /* ============================================================
        STATUS BADGES
        ============================================================ */
        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        /* User statuses */
        .status-aktif        { background: #d1fae5; color: #065f46; }
        .status-tidak_aktif  { background: #fee2e2; color: #991b1b; }
        .status-banned       { background: #1f1f1f; color: #ffffff; }

        /* Generic active/inactive */
        .status-active   { background: #d1fae5; color: #065f46; }
        .status-inactive { background: #fee2e2; color: #991b1b; }

        /* Pembayaran statuses */
        .status-lunas       { background: #d1fae5; color: #065f46; }
        .status-belum_lunas { background: #fee2e2; color: #991b1b; }
        .status-cicilan     { background: #fef3c7; color: #92400e; }

        /* ============================================================
        ROLE / CATEGORY BADGES
        ============================================================ */
        .role-badge {
            padding: 0.25rem 0.625rem;
            border-radius: 4px;
            font-size: 0.6875rem;
            font-weight: 600;
            display: inline-block;
            margin: 0.125rem;
            background: #e0e7ff;
            color: #4338ca;
        }

        /* Semester */
        .semester-badge  { padding: 0.25rem 0.625rem; border-radius: 4px; font-size: 0.6875rem; font-weight: 600; display: inline-block; margin-top: 0.25rem; }
        .semester-ganjil { background: #dcfce7; color: #166534; }
        .semester-genap  { background: #dbeafe; color: #1e40af; }

        /* Pembayaran kategori */
        .kategori-badge       { padding: 0.25rem 0.625rem; border-radius: 4px; font-size: 0.6875rem; font-weight: 600; display: inline-block; }
        .kategori-bulanan     { background: #dbeafe; color: #1e40af; }
        .kategori-tahunan     { background: #e0e7ff; color: #4338ca; }
        .kategori-pendaftaran { background: #fce7f3; color: #9f1239; }
        .kategori-kegiatan   { background: #d1fae5; color: #065f46; }
        .kategori-lainnya     { background: #f3f4f6; color: #374151; }

        /* Mata pelajaran kategori */
        .kategori-agama          { background: #dcfce7; color: #166534; }
        .kategori-umum           { background: #dbeafe; color: #1e40af; }
        .kategori-keterampilan   { background: #fef3c7; color: #92400e; }
        .kategori-ekstrakurikuler { background: #e0e7ff; color: #4338ca; }

        /* ============================================================
        SEMESTER INFO (tahun ajaran)
        ============================================================ */
        .semester-info {
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .semester-dates {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        /* ============================================================
        ALERTS
        ============================================================ */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }
        .alert-warning { background: #fef3c7; color: #92400e; }
        .alert-info    { background: #dbeafe; color: #1e40af; }

        /* ============================================================
        BULK ACTIONS
        ============================================================ */
        .bulk-actions {
            display: none;
            background: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            align-items: center;
            justify-content: space-between;
        }

        .bulk-actions.show {
            display: flex;
        }

        .bulk-info {
            font-size: 0.875rem;
            font-weight: 600;
            color: #92400e;
        }

        /* ============================================================
        CHECKBOXES
        ============================================================ */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-wrapper input[type="checkbox"],
        .role-checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }

        .roles-checkboxes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .role-checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .role-checkbox-item label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            margin: 0;
        }

        /* ============================================================
        CALCULATION SUMMARY
        ============================================================ */
        .calculation-summary {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-top: 1rem;
        }

        .calculation-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }

        .calculation-row.total {
            border-top: 2px solid var(--border-color);
            margin-top: 0.5rem;
            padding-top: 1rem;
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--primary-color);
        }

        /* ============================================================
        MODALS
        ============================================================ */
        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            background: linear-gradient(135deg, #1a3a2e 0%, #0f2419 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem 2rem;
            border: none;
        }

        .modal-header .modal-title {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            background: var(--bg-light);
            border-radius: 0 0 16px 16px;
        }

        /* ============================================================
        PAGINATION
        ============================================================ */
        .pagination {
            gap: 0.25rem;
        }

        .pagination .page-link {
            border: 1.5px solid var(--border-color);
            border-radius: 6px;
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s;
        }

        .pagination .page-link:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .pagination .page-item.disabled .page-link {
            background: var(--bg-light);
            border-color: var(--border-color);
            color: var(--text-muted);
        }

        /* ============================================================
        SELECT2
        ============================================================ */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            padding: 0.25rem;
        }

        /* ============================================================
        LOADING OVERLAY
        ============================================================ */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-overlay.show {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* ============================================================
        ANIMATIONS
        ============================================================ */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ============================================================
        UTILITIES
        ============================================================ */
        .text-muted   { color: var(--text-muted); }
        .text-success { color: var(--color-success); }
        .text-warning { color: var(--color-warning); }
        .text-danger  { color: var(--color-danger); }
        .text-info    { color: var(--color-info); }

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