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
                --clr-primary: #10B981;
                --clr-primary-light: #34D399;
                --clr-primary-dark: #047857;
                --clr-accent: #F59E0B;
                --clr-accent-light: #FBBF24;
                --clr-accent-dark: #D97706;
                --clr-bg: #0F172A;
                --clr-surface: #111827;
                --clr-surface-light: #1F2937;
                --clr-surface-hover: #374151;
                --clr-text: #E5E7EB;
                --clr-text-muted: #9CA3AF;
                --clr-border: #1F2937;
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
                background: #1F2937; /* bg-surface-container */
                border: 1px solid rgba(75, 85, 99, 0.3); /* border-outline-variant/30 */
                border-radius: 24px; /* rounded-3xl */
                padding: 24px; /* px-6 py-6 */
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            }

            .card:hover {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                border-color: rgba(16, 185, 129, 0.4);
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
                background: #1F2937;
                border: 1px solid rgba(75, 85, 99, 0.3);
                border-radius: 24px;
                padding: 24px;
                display: flex;
                align-items: center;
                gap: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transition: transform 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease;
            }

            .stat-card:hover {
                transform: translateY(-4px);
                border-color: #10B981;
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
                border: 1px solid rgba(75, 85, 99, 0.3);
                background: #111827; /* Tailwind bg-surface */
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table th {
                background: #1F2937;
                padding: 16px 24px;
                text-align: left;
                font-size: 0.75rem;
                font-weight: 700;
                color: #9CA3AF;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 1px solid rgba(75, 85, 99, 0.3);
            }

            table td {
                padding: 16px 24px;
                font-size: 0.875rem;
                color: #E5E7EB;
                border-bottom: 1px solid rgba(75, 85, 99, 0.2);
            }

            table tbody tr {
                background: transparent;
                transition: background 0.2s ease;
            }

            table tbody tr:hover {
                background: rgba(31, 41, 55, 0.8); /* hover on surface variant */
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
                background: linear-gradient(135deg, #059669, #10B981); /* Emerald 600 to 500 */
                color: #FFFFFF;
                box-shadow: 0 2px 4px rgba(16,185,129,0.2);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #047857, #059669); /* Emerald 700 to 600 */
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
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
                color: #9CA3AF;
                margin-bottom: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .form-input, .form-select, .form-control {
                width: 100%;
                padding: 11px 14px;
                background: #111827;
                border: 1px solid #1F2937;
                border-radius: 12px;
                color: #E5E7EB;
                font-size: 0.875rem;
                font-family: inherit;
                transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
            }

            .form-input:focus, .form-select:focus, .form-control:focus {
                outline: none;
                border-color: #10B981;
                box-shadow: 0 0 0 1px #10B981;
                background: #111827;
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
        </style>
        <!-- Tailwind CSS & Fonts -->
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
        <script>
            tailwind.config = {
                darkMode: "class",
                theme: { extend: { colors: { 
                    "primary": "#10B981",
                    "primary-container": "#0d9467",
                    "on-primary-container": "#d1fae5",
                    "secondary": "#1F2937",
                    "accent": "#F59E0B",
                    "background": "#0F172A",
                    "surface": "#111827",
                    "surface-container": "#1F2937",
                    "surface-container-low": "#111827",
                    "surface-container-high": "#374151",
                    "surface-container-highest": "#4B5563",
                    "surface-container-lowest": "#060e20",
                    "on-surface": "#E5E7EB",
                    "on-surface-variant": "#9CA3AF",
                    "outline-variant": "#4B5563",
                    "error": "#EF4444",
                    "tertiary": "#F59E0B"
                }, fontFamily: { "headline": ["Inter"], "body": ["Inter"], "label": ["Inter"] } } }
            }
        </script>
        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; border-radius: 10px; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
            
            /* Phase 2 Input Constraints */
            input.form-control, input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="number"], select.form-select, textarea {
                background-color: #111827 !important;
                color: #E5E7EB !important;
                border: 1px solid #374151 !important;
            }
            input:focus, select:focus, textarea:focus {
                border-color: #10B981 !important;
                outline: none !important;
                box-shadow: 0 0 0 1px #10B981 !important;
            }
        </style>
    </head>
    <body class="bg-background text-on-surface selection:bg-primary/30 flex w-full min-h-screen overflow-hidden font-['Inter']">
        
        <!-- Mobile Toggle: Fixed ID and Toggle Logic -->
        <button class="mobile-toggle text-on-surface bg-surface-container rounded-lg p-2 flex items-center justify-center border border-outline-variant/20 shadow-sm cursor-pointer lg:hidden fixed top-4 left-4" style="z-index:90;" onclick="toggleSidebarMenu()" id="sidebar-toggle">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div class="mobile-overlay lg:hidden fixed inset-0 bg-black/50 hidden" style="z-index:80; transition: opacity 0.3s;" id="mobile-overlay" onclick="toggleSidebarMenu()"></div>

        <!-- Sidebar Layout Component -->
        <aside class="sidebar fixed left-0 top-0 h-full flex flex-col w-64 border-r border-outline-variant/15 bg-surface-container-low shadow-2xl transition-transform duration-300 -translate-x-full lg:translate-x-0" style="z-index:85;" id="sidebar">
            <div class="px-6 py-6 flex items-center gap-3">
                <img src="{{ asset('storage/logo/Logo.svg') }}" alt="ImamKu Logo" class="h-10 w-auto object-contain">
                <div>
                    <h1 class="text-xl font-bold tracking-tighter text-primary">ImamKu</h1>
                    <p class="text-[10px] uppercase tracking-widest text-on-surface-variant/60">Management System</p>
                </div>
            </div>

            <nav class="sidebar-nav flex-1 overflow-y-auto custom-scrollbar px-4 space-y-2 pb-8" style="padding-top:0;">
                @if(auth()->check())
                    <x-sidebar-nav :role="auth()->user()->isAdmin() ? 'admin' : 'imam'" />
                @endif
            </nav>

            @if(auth()->check())
            <div class="sidebar-user p-4 bg-surface-container-lowest/50 border-t border-outline-variant/10 text-left" style="margin-top:auto;">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-surface-container-high/40">
                    <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xs uppercase">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    <div class="flex-1 overflow-hidden" style="text-align:left;">
                        <p class="text-xs font-semibold truncate text-on-surface m-0" style="margin:0;">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-on-surface-variant truncate capitalize m-0" style="margin:0;">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 flex items-center">
                        @csrf
                        <button type="submit" class="material-symbols-outlined text-sm text-on-surface-variant cursor-pointer hover:text-primary transition-colors focus:outline-none" style="background:none;border:none;" title="Logout">logout</button>
                    </form>
                </div>
            </div>
            @endif
        </aside>

        <!-- Main Workspace -->
        <div class="flex-1 h-screen flex flex-col bg-surface transition-all duration-300 overflow-y-auto lg:ml-64 w-full" id="main-scroll-wrapper">
            
            <x-top-header />
            
            <main class="main-content w-full flex-1" style="margin-left:0 !important; padding:24px 32px !important; min-height:auto;">
                @if(session('success'))
                    <div class="p-4 mb-4 rounded-xl bg-primary/10 border border-primary/20 text-primary text-sm font-medium flex items-center gap-2 alert alert-success"><span class="material-symbols-outlined text-lg">check_circle</span>{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="p-4 mb-4 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-medium flex items-center gap-2 alert alert-error"><span class="material-symbols-outlined text-lg">error</span>{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="p-4 mb-4 rounded-xl bg-error/10 border border-error/20 text-error text-sm font-medium alert alert-error">
                        <div class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-lg mt-0.5">warning</span>
                            <div>
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="w-full pb-32">
                    @yield('content')
                </div>
            </main>
        </div>

        <script>
            function toggleSidebarMenu() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobile-overlay');
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                }
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

        @if(auth()->check() && auth()->user()->role === 'imam' && auth()->user()->unreadNotifications->count() > 0)
            <!-- Auto-play notification sound if there are unread notifications (Imam only) -->
            <audio id="notification-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" autoplay></audio>
        @endif

        @if(auth()->check())
            <!-- Mandatory Permission Check Modal -->
            <div id="globalPermissionModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(10,15,26,0.95); z-index:99999; justify-content:center; align-items:center; backdrop-filter:blur(10px);">
                <div class="card" style="width:100%; max-width:450px; margin:20px; padding:30px; text-align:center; border:1px solid var(--clr-accent);">
                    <div style="background:var(--clr-surface-light); width:80px; height:80px; border-radius:50%; display:flex; justify-content:center; align-items:center; margin:0 auto 20px;">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--clr-accent)"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4M12 16h.01"/></svg>
                    </div>
                    
                    <h2 style="margin-bottom:10px; color:var(--clr-text)">Izin Sistem Diperlukan</h2>
                    <p style="font-size:0.9rem; color:var(--clr-text-muted); margin-bottom:24px; line-height:1.5;">Untuk menggunakan sistem ImamKu dengan lancar, terutama saat melakukan absensi, sistem membutuhkan akses wajib ke beberapa fitur perangkat Anda.</p>
                    
                    <div style="text-align:left; margin-bottom:24px; background:var(--clr-bg); padding:16px; border-radius:8px;">
                        <div id="check-loc" style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                            <span class="status-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></span> <strong>Lokasi (GPS)</strong> <span style="font-size:0.75rem;color:var(--clr-text-muted);margin-left:auto">Wajib untuk validasi radius</span>
                        </div>
                        <div id="check-cam" style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                            <span class="status-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg></span> <strong>Kamera</strong> <span style="font-size:0.75rem;color:var(--clr-text-muted);margin-left:auto">Wajib untuk foto bukti</span>
                        </div>
                        <div id="check-notif" style="display:flex; align-items:center; gap:10px;">
                            <span class="status-icon"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/></svg></span> <strong>Notifikasi</strong> <span style="font-size:0.75rem;color:var(--clr-text-muted);margin-left:auto">Wajib untuk peringatan swap</span>
                        </div>
                    </div>

                    <button id="btnGrantPermissions" class="btn btn-primary" style="width:100%; padding:14px; font-size:1rem; display:flex; justify-content:center; gap:8px;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg> 
                        Berikan Semua Izin
                    </button>
                    
                    <p id="permissionErrorHelp" style="display:none; color:var(--clr-danger); font-size:0.8rem; margin-top:16px;">Jika pop-up tidak muncul, klik ikon "Gembok" (Lock) di address bar browser Anda, lalu pastikan Lokasi, Kamera, dan Notifikasi diubah menjadi "Allow/Izinkan", kemudian muat ulang halaman (Refresh).</p>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('globalPermissionModal');
                    const btn = document.getElementById('btnGrantPermissions');
                    const errHelp = document.getElementById('permissionErrorHelp');

                    let permLoc = false, permCam = false, permNotif = false;

                    function updateUI(id, granted) {
                        const el = document.getElementById(id).querySelector('.status-icon');
                        if (granted) {
                            el.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>';
                            el.style.color = 'var(--clr-success)';
                        } else {
                            el.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>';
                            el.style.color = 'var(--clr-danger)';
                        }
                    }

                    async function checkAllPermissions() {
                        // Check Notification
                        if ("Notification" in window) {
                            permNotif = (Notification.permission === 'granted');
                            updateUI('check-notif', permNotif);
                        } else { permNotif = true; } // ignore if browser doesn't support

                        // For Location & Camera, we usually just have to prompt.
                        // We can check using permissions API where supported
                        try {
                            const pLoc = await navigator.permissions.query({name: 'geolocation'});
                            permLoc = (pLoc.state === 'granted');
                            updateUI('check-loc', permLoc);
                        } catch(e) {}

                        try {
                            const pCam = await navigator.permissions.query({name: 'camera'});
                            permCam = (pCam.state === 'granted');
                            updateUI('check-cam', permCam);
                        } catch(e) {}

                        // Force modal if any essential permission is missing
                        if (!permLoc || !permCam || !permNotif) {
                            modal.style.display = 'flex';
                            document.body.style.overflow = 'hidden';
                        } else {
                            modal.style.display = 'none';
                            document.body.style.overflow = 'auto';
                        }
                    }

                    checkAllPermissions(); // Initial check

                    btn.addEventListener('click', async function() {
                        btn.innerHTML = 'Memproses izin aplikasi...';
                        btn.disabled = true;
                        errHelp.style.display = 'block';

                        // 1. Request Notification
                        if ("Notification" in window && Notification.permission !== "granted") {
                            await Notification.requestPermission();
                        }

                        // 2. Request Camera
                        try {
                            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                            stream.getTracks().forEach(track => track.stop()); // close immediately
                            permCam = true;
                            updateUI('check-cam', true);
                        } catch (err) {
                            console.error("Camera denied", err);
                        }

                        // 3. Request Location
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                function(pos) {
                                    permLoc = true;
                                    updateUI('check-loc', true);
                                    btn.innerHTML = 'Berikan Semua Izin';
                                    btn.disabled = false;
                                    checkAllPermissions(); // Re-evaluate and close if all good
                                },
                                function(err) {
                                    console.error("Location denied", err);
                                    btn.innerHTML = 'Coba Lagi / Berikan Izin';
                                    btn.disabled = false;
                                },
                                { enableHighAccuracy: true, timeout: 5000 }
                            );
                        }
                    });
                });
            </script>
        @endif

        @stack('scripts')
    </body>
</html>
