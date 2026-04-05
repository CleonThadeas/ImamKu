<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="ImamKu - Sistem Manajemen Penjadwalan Imam Masjid Ramadan">

        <title>{{ config('app.name', 'ImamKu') }} — @yield('title', 'Dashboard')</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('storage/logo/Logo.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --clr-primary: #1B4332;
                --clr-primary-light: #2D6A4F;
                --clr-primary-dark: #0F2B1F;
                --clr-accent: #D4A843;
                --clr-accent-light: #E8C96A;
                --clr-accent-dark: #B8922D;
                --clr-bg: #0A0F1A;
                --clr-surface: #111827;
                --clr-surface-light: #1F2937;
                --clr-surface-hover: #283343;
                --clr-text: #F9FAFB;
                --clr-text-muted: #9CA3AF;
                --clr-border: #374151;
                --clr-success: #10B981;
                --clr-danger: #EF4444;
                --clr-warning: #F59E0B;
                --clr-info: #3B82F6;
                --sidebar-width: 260px;
            }

            * { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--clr-bg);
                color: var(--clr-text);
                min-height: 100vh;
                overflow-x: hidden;
            }

            /* ── Page Transition ─────────────────────── */
            @keyframes pageEnter {
                from { opacity: 0; transform: translateY(12px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .main-content { animation: pageEnter 0.4s ease-out; }

            /* ── Utility ─────────────────────────────── */
            .mb-2 { margin-bottom: 8px; }
            .mb-3 { margin-bottom: 16px; }
            .mb-4 { margin-bottom: 24px; }
            .mt-2 { margin-top: 8px; }
            .mt-3 { margin-top: 16px; }
            .mt-4 { margin-top: 24px; }
            .text-muted { color: var(--clr-text-muted); }
            .text-center { text-align: center; }
            .gap-sm { gap: 8px; }
            .gap-md { gap: 16px; }

            /* ── Sidebar ─────────────────────────────── */
            .sidebar {
                position: fixed;
                left: 0; top: 0; bottom: 0;
                width: var(--sidebar-width);
                background: linear-gradient(180deg, var(--clr-primary-dark) 0%, var(--clr-primary) 50%, var(--clr-primary-light) 100%);
                padding: 0;
                z-index: 50;
                display: flex;
                flex-direction: column;
                transition: transform 0.3s ease;
                box-shadow: 4px 0 20px rgba(0,0,0,0.3);
            }

            .sidebar-brand {
                padding: 24px 20px;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                text-align: center;
            }

            .sidebar-brand h1 {
                font-size: 1.5rem;
                font-weight: 800;
                color: var(--clr-accent);
                letter-spacing: 1px;
            }

            .sidebar-brand .brand-sub {
                font-size: 0.7rem;
                color: rgba(255,255,255,0.6);
                text-transform: uppercase;
                letter-spacing: 2px;
                margin-top: 4px;
            }

            .sidebar-nav {
                flex: 1;
                padding: 16px 12px;
                overflow-y: auto;
            }

            .sidebar-nav .nav-section {
                font-size: 0.65rem;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: rgba(255,255,255,0.4);
                padding: 12px 12px 6px;
                margin-top: 8px;
            }

            .sidebar-nav a {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 14px;
                color: rgba(255,255,255,0.7);
                text-decoration: none;
                border-radius: 8px;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 0.2s ease;
                margin-bottom: 2px;
            }

            .sidebar-nav a:hover {
                background: rgba(255,255,255,0.1);
                color: #fff;
                transform: translateX(4px);
            }

            .sidebar-nav a.active {
                background: rgba(212, 168, 67, 0.2);
                color: var(--clr-accent);
                border-left: 3px solid var(--clr-accent);
            }

            .sidebar-nav a .nav-icon {
                width: 20px;
                text-align: center;
                font-size: 1rem;
            }

            .sidebar-user {
                padding: 16px;
                border-top: 1px solid rgba(255,255,255,0.1);
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .sidebar-user .user-avatar {
                width: 36px; height: 36px;
                background: var(--clr-accent);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 0.875rem;
                color: var(--clr-primary-dark);
            }

            .sidebar-user .user-info {
                flex: 1;
                min-width: 0;
            }

            .sidebar-user .user-name {
                font-size: 0.8rem;
                font-weight: 600;
                color: #fff;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .sidebar-user .user-role {
                font-size: 0.65rem;
                color: var(--clr-accent);
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            /* ── Main Content ────────────────────────── */
            .main-content {
                margin-left: var(--sidebar-width);
                min-height: 100vh;
                padding: 24px 32px;
            }

            .main-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 32px;
            }

            .main-header h2 {
                font-size: 1.5rem;
                font-weight: 700;
                color: var(--clr-text);
            }

            .main-header .breadcrumb {
                font-size: 0.8rem;
                color: var(--clr-text-muted);
                margin-top: 4px;
            }

            /* ── Mobile Toggle ───────────────────────── */
            .mobile-toggle {
                display: none;
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 60;
                background: var(--clr-primary);
                color: var(--clr-accent);
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 8px;
                font-size: 1.2rem;
                cursor: pointer;
            }

            .mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 40;
            }

            /* ── Cards ───────────────────────────────── */
            .card {
                background: var(--clr-surface);
                border: 1px solid var(--clr-border);
                border-radius: 16px;
                padding: 24px;
                backdrop-filter: blur(10px);
                transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            }

            .card:hover {
                box-shadow: 0 8px 32px rgba(0,0,0,0.2);
                border-color: rgba(212,168,67,0.15);
            }

            .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .card-title {
                font-size: 1rem;
                font-weight: 600;
                color: var(--clr-text);
            }

            /* ── Stats Grid ──────────────────────────── */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px;
                margin-bottom: 32px;
            }

            .stat-card {
                background: linear-gradient(135deg, var(--clr-surface) 0%, var(--clr-surface-light) 100%);
                border: 1px solid var(--clr-border);
                border-radius: 16px;
                padding: 22px;
                display: flex;
                align-items: center;
                gap: 16px;
                transition: transform 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
            }

            .stat-card:hover {
                transform: translateY(-4px);
                border-color: var(--clr-accent);
                box-shadow: 0 12px 28px rgba(0,0,0,0.18);
            }

            .stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.3rem;
            }

            .stat-icon.green { background: rgba(16, 185, 129, 0.15); color: var(--clr-success); }
            .stat-icon.gold  { background: rgba(212, 168, 67, 0.15); color: var(--clr-accent); }
            .stat-icon.blue  { background: rgba(59, 130, 246, 0.15); color: var(--clr-info); }
            .stat-icon.red   { background: rgba(239, 68, 68, 0.15); color: var(--clr-danger); }

            .stat-value {
                font-size: 1.5rem;
                font-weight: 700;
                line-height: 1;
            }

            .stat-label {
                font-size: 0.75rem;
                color: var(--clr-text-muted);
                margin-top: 4px;
            }

            /* ── Table ───────────────────────────────── */
            .table-wrapper {
                overflow-x: auto;
                border-radius: 12px;
                border: 1px solid var(--clr-border);
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table th {
                background: var(--clr-surface-light);
                padding: 12px 16px;
                text-align: left;
                font-size: 0.75rem;
                font-weight: 600;
                color: var(--clr-text-muted);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 1px solid var(--clr-border);
            }

            table td {
                padding: 12px 16px;
                font-size: 0.875rem;
                color: var(--clr-text);
                border-bottom: 1px solid var(--clr-border);
            }

            table tbody tr {
                transition: background 0.2s ease;
            }

            table tbody tr:hover {
                background: var(--clr-surface-hover);
            }

            table tbody tr:nth-child(even) {
                background: rgba(255,255,255,0.015);
            }

            table tbody tr:nth-child(even):hover {
                background: var(--clr-surface-hover);
            }

            /* ── Buttons ─────────────────────────────── */
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                border-radius: 8px;
                font-size: 0.875rem;
                font-weight: 600;
                text-decoration: none;
                cursor: pointer;
                border: none;
                transition: all 0.2s ease;
                font-family: inherit;
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--clr-accent-dark), var(--clr-accent));
                color: var(--clr-primary-dark);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, var(--clr-accent), var(--clr-accent-light));
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(212, 168, 67, 0.3);
            }

            .btn-secondary {
                background: var(--clr-surface-light);
                color: var(--clr-text);
                border: 1px solid var(--clr-border);
            }

            .btn-secondary:hover {
                background: var(--clr-surface-hover);
                border-color: var(--clr-accent);
            }

            .btn-success {
                background: linear-gradient(135deg, #059669, var(--clr-success));
                color: #fff;
            }

            .btn-danger {
                background: linear-gradient(135deg, #DC2626, var(--clr-danger));
                color: #fff;
            }

            .btn-danger:hover, .btn-success:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }

            .btn-sm {
                padding: 6px 14px;
                font-size: 0.75rem;
            }

            .btn-xs {
                padding: 4px 10px;
                font-size: 0.7rem;
            }

            /* ── Forms ───────────────────────────────── */
            .form-group {
                margin-bottom: 20px;
            }

            .form-label {
                display: block;
                font-size: 0.8rem;
                font-weight: 600;
                color: var(--clr-text-muted);
                margin-bottom: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .form-input, .form-select, .form-control {
                width: 100%;
                padding: 11px 14px;
                background: var(--clr-surface-light);
                border: 1.5px solid var(--clr-border);
                border-radius: 10px;
                color: var(--clr-text);
                font-size: 0.875rem;
                font-family: inherit;
                transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
            }

            .form-input:focus, .form-select:focus, .form-control:focus {
                outline: none;
                border-color: var(--clr-accent);
                box-shadow: 0 0 0 3px rgba(212, 168, 67, 0.12);
                background: var(--clr-surface);
            }

            .form-input::placeholder, .form-control::placeholder {
                color: var(--clr-text-muted);
                opacity: 0.6;
            }

            .form-select option {
                background: var(--clr-surface);
            }

            .form-checkbox-wrapper {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .form-checkbox {
                width: 18px;
                height: 18px;
                accent-color: var(--clr-accent);
            }

            /* ── Channel Chip Toggles ────────────────── */
            .channel-chips {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .channel-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                background: var(--clr-surface-light);
                border: 1.5px solid var(--clr-border);
                border-radius: 100px;
                font-size: 0.8rem;
                font-weight: 500;
                color: var(--clr-text-muted);
                cursor: pointer;
                transition: all 0.25s ease;
                user-select: none;
            }

            .channel-chip:hover {
                border-color: var(--clr-accent);
                color: var(--clr-text);
            }

            .channel-chip input[type="checkbox"] {
                accent-color: var(--clr-accent);
                width: 15px;
                height: 15px;
            }

            .channel-chip:has(input:checked) {
                border-color: var(--clr-accent);
                background: rgba(212,168,67,0.1);
                color: var(--clr-accent);
            }

            /* ── Alerts ──────────────────────────────── */
            .alert {
                padding: 14px 20px;
                border-radius: 10px;
                margin-bottom: 20px;
                font-size: 0.875rem;
                display: flex;
                align-items: center;
                gap: 10px;
                animation: slideDown 0.3s ease;
            }

            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .alert-success {
                background: rgba(16, 185, 129, 0.1);
                border: 1px solid rgba(16, 185, 129, 0.3);
                color: var(--clr-success);
            }

            .alert-error {
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid rgba(239, 68, 68, 0.3);
                color: var(--clr-danger);
            }

            .alert-warning {
                background: rgba(245, 158, 11, 0.1);
                border: 1px solid rgba(245, 158, 11, 0.3);
                color: var(--clr-warning);
            }

            /* ── Badge ───────────────────────────────── */
            .badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 10px;
                border-radius: 100px;
                font-size: 0.7rem;
                font-weight: 600;
                letter-spacing: 0.5px;
            }

            .badge-success { background: rgba(16,185,129,0.15); color: var(--clr-success); }
            .badge-danger  { background: rgba(239,68,68,0.15); color: var(--clr-danger); }
            .badge-warning { background: rgba(245,158,11,0.15); color: var(--clr-warning); }
            .badge-info    { background: rgba(59,130,246,0.15); color: var(--clr-info); }
            .badge-gold    { background: rgba(212,168,67,0.15); color: var(--clr-accent); }
            .badge-neutral { background: rgba(156,163,175,0.15); color: var(--clr-text-muted); }

            /* ── Imam Colors ─────────────────────────── */
            .imam-color-1 { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border-color: #3B82F6; }
            .imam-color-2 { background: rgba(16, 185, 129, 0.15); color: #34D399; border-color: #10B981; }
            .imam-color-3 { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border-color: #F59E0B; }
            .imam-color-4 { background: rgba(239, 68, 68, 0.15); color: #F87171; border-color: #EF4444; }
            .imam-color-5 { background: rgba(168, 85, 247, 0.15); color: #C084FC; border-color: #A855F7; }

            /* ── Schedule Calendar ────────────────────── */
            .schedule-grid {
                display: grid;
                gap: 1px;
                background: var(--clr-border);
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid var(--clr-border);
            }

            .schedule-cell {
                background: var(--clr-surface);
                padding: 8px 10px;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                transition: background 0.15s;
            }

            .schedule-cell:hover {
                background: var(--clr-surface-hover);
            }

            .schedule-cell.header {
                background: var(--clr-surface-light);
                font-weight: 600;
                color: var(--clr-text-muted);
                text-transform: uppercase;
                font-size: 0.65rem;
                letter-spacing: 1px;
            }

            .schedule-cell.date-cell {
                background: var(--clr-surface-light);
                font-weight: 600;
                color: var(--clr-accent);
                font-size: 0.7rem;
                flex-direction: column;
                gap: 2px;
            }

            .schedule-cell .imam-tag {
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 0.65rem;
                font-weight: 600;
                border: 1px solid;
                cursor: pointer;
                white-space: nowrap;
                transition: transform 0.15s;
            }

            .schedule-cell .imam-tag:hover {
                transform: scale(1.05);
            }

            .schedule-cell .empty-slot {
                color: var(--clr-text-muted);
                font-style: italic;
                font-size: 0.65rem;
            }

            .schedule-cell.my-schedule {
                background: rgba(212, 168, 67, 0.05);
                box-shadow: inset 0 0 0 2px rgba(212, 168, 67, 0.2);
            }

            /* ── Empty State ─────────────────────────── */
            .empty-state {
                text-align: center;
                padding: 56px 24px;
                color: var(--clr-text-muted);
                animation: pageEnter 0.5s ease-out;
            }

            .empty-state .empty-icon {
                font-size: 3rem;
                margin-bottom: 16px;
                opacity: 0.4;
            }

            .empty-state h3 {
                margin-bottom: 8px;
                color: var(--clr-text);
                font-size: 1.1rem;
            }

            .empty-state p {
                font-size: 0.85rem;
                max-width: 400px;
                margin: 0 auto;
                line-height: 1.6;
            }

            /* ── Modal ───────────────────────────────── */
            .modal-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.6);
                z-index: 100;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(4px);
            }

            .modal-overlay.active {
                display: flex;
            }

            .modal-content {
                background: var(--clr-surface);
                border: 1px solid var(--clr-border);
                border-radius: 16px;
                padding: 32px;
                min-width: 400px;
                max-width: 500px;
                width: 90%;
                animation: modalIn 0.3s ease;
            }

            @keyframes modalIn {
                from { opacity: 0; transform: scale(0.95) translateY(20px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }

            .modal-title {
                font-size: 1.1rem;
                font-weight: 700;
                margin-bottom: 20px;
                color: var(--clr-accent);
            }

            /* ── Pagination ──────────────────────────── */
            .pagination-wrapper {
                margin-top: 20px;
            }

            .pagination-wrapper nav > div:first-child { display: none; }

            .pagination-wrapper span, .pagination-wrapper a {
                padding: 6px 12px;
                font-size: 0.8rem;
                background: var(--clr-surface);
                border: 1px solid var(--clr-border);
                color: var(--clr-text-muted);
                border-radius: 6px;
                margin-right: 4px;
            }

            .pagination-wrapper span[aria-current] {
                background: var(--clr-accent);
                color: var(--clr-primary-dark);
                border-color: var(--clr-accent);
                font-weight: 600;
            }

            .pagination-wrapper a:hover {
                border-color: var(--clr-accent);
                color: var(--clr-accent);
            }

            /* ── Grid Layouts ────────────────────────── */
            .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
            .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

            /* ── Responsive: Tablet ──────────────────── */
            @media (max-width: 1024px) {
                .main-content {
                    padding: 24px 20px;
                }

                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .grid-3 {
                    grid-template-columns: 1fr 1fr;
                }
            }

            /* ── Responsive: Mobile ──────────────────── */
            @media (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                }

                .sidebar.open {
                    transform: translateX(0);
                }

                .mobile-toggle {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .mobile-overlay.active {
                    display: block;
                }

                .main-content {
                    margin-left: 0;
                    padding: 68px 16px 24px;
                }

                .main-header {
                    flex-direction: column;
                    align-items: flex-start !important;
                    gap: 12px;
                }

                .main-header h2 {
                    font-size: 1.2rem;
                }

                .stats-grid {
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                }

                .stat-card {
                    padding: 14px;
                }

                .stat-value {
                    font-size: 1.2rem;
                }

                .grid-2, .grid-3 {
                    grid-template-columns: 1fr;
                }

                .modal-content {
                    min-width: unset;
                    padding: 20px;
                }

                .card {
                    padding: 16px;
                    border-radius: 12px;
                }

                table th, table td {
                    padding: 10px 12px;
                    font-size: 0.8rem;
                }
            }

            /* ── Responsive: Small Mobile ────────────── */
            @media (max-width: 480px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .main-content {
                    padding: 64px 12px 20px;
                }

                .btn {
                    padding: 8px 14px;
                    font-size: 0.8rem;
                }

                .card-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 10px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Mobile Toggle -->
        <button class="mobile-toggle" onclick="toggleSidebar()" id="sidebar-toggle"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
        <div class="mobile-overlay" id="mobile-overlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand" style="padding: 24px 20px; display:flex; flex-direction:column; align-items:center;">
                <img src="{{ asset('storage/logo/Logo.svg') }}" alt="ImamKu Logo" style="height: 48px; width: auto; object-fit: contain;">
                <div class="brand-sub" style="margin-top: 8px;">Ramadan Schedule</div>
            </div>

            <nav class="sidebar-nav">
                @if(auth()->user()->isAdmin())
                    <div class="nav-section">Menu Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span> Dashboard
                    </a>
                    <a href="{{ route('admin.schedules.index') }}" class="{{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></span> Jadwal
                    </a>
                    <a href="{{ route('admin.prayer-times.index') }}" class="{{ request()->routeIs('admin.prayer-times.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span> Waktu Sholat
                    </a>

                    <div class="nav-section">Manajemen</div>
                    <a href="{{ route('admin.imams.index') }}" class="{{ request()->routeIs('admin.imams.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span> Data Imam
                    </a>
                    <a href="{{ route('admin.seasons.index') }}" class="{{ request()->routeIs('admin.seasons.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></span> Season Ramadan
                    </a>
                    <a href="{{ route('admin.attendances.index') }}" class="{{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2v11z"/><circle cx="12" cy="13" r="4"/></svg></span> Validasi Absen
                    </a>
                    <a href="{{ route('admin.swaps.index') }}" class="{{ request()->routeIs('admin.swaps.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 21l6-6M9 8l-6 6M4 14v5h5M4 19l6-6M3 3l6 6"/></svg></span> Monitoring Swap
                    </a>

                    <div class="nav-section">Keuangan</div>
                    <a href="{{ route('admin.fees.index') }}" class="{{ request()->routeIs('admin.fees.index') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span> Konfigurasi Fee
                    </a>
                    <a href="{{ route('admin.fees.report') }}" class="{{ request()->routeIs('admin.fees.report') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg></span> Laporan Fee
                    </a>

                    <div class="nav-section">Lainnya</div>
                    <a href="{{ route('admin.exports.index') }}" class="{{ request()->routeIs('admin.exports.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg></span> Pusat Export
                    </a>
                    <a href="{{ route('admin.notification-logs.index') }}" class="{{ request()->routeIs('admin.notification-logs.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></span> Log Notifikasi
                    </a>
                    <a href="{{ route('admin.broadcast.index') }}" class="{{ request()->routeIs('admin.broadcast.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></span> Broadcast
                    </a>
                @else
                    <div class="nav-section">Menu</div>
                    <a href="{{ route('imam.dashboard') }}" class="{{ request()->routeIs('imam.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span> Dashboard
                    </a>
                    <a href="{{ route('imam.schedules.index') }}" class="{{ request()->routeIs('imam.schedules.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></span> Lihat Jadwal
                    </a>
                    <a href="{{ route('imam.swaps.index') }}" class="{{ request()->routeIs('imam.swaps.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg></span> Swap Jadwal
                    </a>
                    <a href="{{ route('imam.fees.index') }}" class="{{ request()->routeIs('imam.fees.*') ? 'active' : '' }}">
                        <span class="nav-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></span> Laporan Pendapatan
                    </a>
                    <a href="{{ route('imam.notifications.index') }}" class="{{ request()->routeIs('imam.notifications.*') ? 'active' : '' }}">
                        <span class="nav-icon" style="position:relative">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span style="position:absolute;top:-2px;right:-2px;width:6px;height:6px;background:var(--clr-danger);border-radius:50%"></span>
                            @endif
                        </span> Kotak Masuk
                    </a>
                @endif
            </nav>

            <div class="sidebar-user">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:rgba(255,255,255,0.5);cursor:pointer" title="Logout"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg></button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('open');
                document.getElementById('mobile-overlay').classList.toggle('active');
            }

            @if(request()->routeIs('*.dashboard') || request()->routeIs('*.swaps.index') || request()->routeIs('*.notifications.index') || request()->routeIs('*.schedules.index'))
            // Dashboard Real-time Polling: updates UI seamlessly
            setInterval(() => {
                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Update main content dynamically without hard reload
                        const currentMain = document.querySelector('.main-content');
                        const newMain = doc.querySelector('.main-content');
                        if (currentMain && newMain && currentMain.innerHTML !== newMain.innerHTML) {
                            currentMain.innerHTML = newMain.innerHTML;
                        }

                        // Update Sidebar Badge (Notifications)
                        const currentNav = document.querySelector('.sidebar-nav');
                        const newNav = doc.querySelector('.sidebar-nav');
                        if (currentNav && newNav && currentNav.innerHTML !== newNav.innerHTML) {
                            currentNav.innerHTML = newNav.innerHTML;
                        }

                        // Check new sound notification
                        const newSound = doc.getElementById('notification-sound');
                        if (newSound && !document.getElementById('notification-sound')) {
                            document.body.appendChild(newSound);
                            newSound.play().catch(e => {});
                        }
                    }).catch(console.error);
            }, 10000);
            @endif
        </script>

        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
            <!-- Auto-play notification sound if there are unread notifications -->
            <audio id="notification-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" autoplay></audio>
        @endif

        @stack('scripts')
    </body>
</html>
