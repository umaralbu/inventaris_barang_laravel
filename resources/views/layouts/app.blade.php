<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventaris') | Perpustakaan UMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;900&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #f5f3ee;
            --bg-secondary: #f1efe9;
            --surface: rgba(255, 255, 255, 0.9);
            --surface-soft: rgba(245, 243, 238, 0.9);
            --border: rgba(148, 163, 184, 0.24);
            --text: #2d2d2d;
            --muted: #6b7280;
            --primary: #b7a58a;
            --primary-soft: rgba(183, 165, 138, 0.14);
            --success: #6f9f77;
            --warning: #d9b980;
            --danger: #c87d6a;
            --shadow: 0 18px 40px rgba(111, 116, 122, 0.12);
            --sidebar-bg: linear-gradient(180deg, #f7f4ef, #f0ece5);
            --topbar-bg: rgba(255, 255, 255, 0.8);
            --card-bg: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(243,239,232,0.9));
            --table-head: linear-gradient(180deg, #e9d7b5, #dcc19a);
            --table-head-text: #2f2a25;
            --input-bg: rgba(255, 255, 255, 0.7);
        }

        body.dark-mode {
            --bg-primary: #0f172a;
            --bg-secondary: #111827;
            --surface: rgba(15, 23, 42, 0.92);
            --surface-soft: rgba(17, 24, 39, 0.9);
            --border: rgba(148, 163, 184, 0.22);
            --text: #e5e7eb;
            --muted: #cbd5e1;
            --primary: #d4b98d;
            --primary-soft: rgba(212, 185, 141, 0.12);
            --success: #86efac;
            --warning: #fbbf24;
            --danger: #fca5a5;
            --shadow: 0 18px 40px rgba(2, 6, 23, 0.5);
            --sidebar-bg: linear-gradient(180deg, #111827, #0f172a);
            --topbar-bg: rgba(15, 23, 42, 0.8);
            --card-bg: linear-gradient(180deg, rgba(17, 24, 39, 0.96), rgba(15, 23, 42, 0.92));
            --table-head: linear-gradient(180deg, #1f2937, #111827);
            --table-head-text: #f8fafc;
            --input-bg: rgba(30, 41, 59, 0.8);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background:
                radial-gradient(circle at top left, rgba(183, 165, 138, 0.18), transparent 25%),
                radial-gradient(circle at bottom right, rgba(148, 163, 184, 0.18), transparent 30%),
                var(--bg-primary);
            color: var(--text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            transition: background 0.25s ease, color 0.25s ease;
        }

        body.dark-mode .sidebar {
            background: var(--sidebar-bg);
            border-right-color: rgba(148, 163, 184, 0.2);
        }

        body.dark-mode .brand,
        body.dark-mode .nav-link,
        body.dark-mode .topbar h4,
        body.dark-mode .card,
        body.dark-mode .table,
        body.dark-mode .table th,
        body.dark-mode .table td,
        body.dark-mode .stats-card h3,
        body.dark-mode .stats-card .text-muted,
        body.dark-mode .modal-content,
        body.dark-mode .modal-title,
        body.dark-mode .page-header h3,
        body.dark-mode .page-header,
        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode .btn-outline-light,
        body.dark-mode .login-card,
        body.dark-mode .login-card h3,
        body.dark-mode .login-card .subtitle,
        body.dark-mode .dashboard-hero,
        body.dark-mode .stock-warning-card {
            color: var(--text);
        }

        body.dark-mode .sidebar {
            box-shadow: inset -1px 0 0 rgba(255,255,255,0.04);
        }

        body.dark-mode .nav-link {
            color: #dfe7f2;
        }

        body.dark-mode .nav-link:hover,
        body.dark-mode .nav-link.active {
            background: rgba(212, 185, 141, 0.14);
            color: #fff;
        }

        body.dark-mode .topbar,
        body.dark-mode .card,
        body.dark-mode .modal-content,
        body.dark-mode .login-card,
        body.dark-mode .dashboard-hero,
        body.dark-mode .stock-warning-card,
        body.dark-mode .table-wrap {
            background: var(--card-bg);
            border-color: rgba(148, 163, 184, 0.2);
        }

        body.dark-mode .topbar {
            background: var(--topbar-bg);
        }

        body.dark-mode .table thead th {
            background: var(--table-head);
            color: var(--table-head-text);
        }

        body.dark-mode .table,
        body.dark-mode .table th,
        body.dark-mode .table td {
            color: var(--text);
        }

        body.dark-mode .table tbody tr:hover {
            background: rgba(148, 163, 184, 0.08);
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode .form-check-input {
            background: var(--input-bg);
            border-color: rgba(148, 163, 184, 0.3);
            color: var(--text);
        }

        body.dark-mode .btn-outline-light {
            border-color: rgba(148, 163, 184, 0.4);
            background: rgba(15, 23, 42, 0.5);
            color: var(--text);
        }

        body.dark-mode .btn-outline-light:hover {
            background: rgba(148, 163, 184, 0.12);
        }

        body.dark-mode .dashboard-hero {
            background: linear-gradient(135deg, rgba(59,130,246,0.18), rgba(16,185,129,0.08));
            border-color: rgba(96,165,250,0.3);
        }

        body.dark-mode .stats-card .text-muted,
        body.dark-mode .stats-card h3 {
            color: #ffffff !important;
        }

        body.dark-mode .stats-card .text-muted {
            opacity: 1;
        }

        body.dark-mode .stat-detail-item {
            background: rgba(148, 163, 184, 0.08);
        }

        body.dark-mode .alert-light {
            background: rgba(15, 23, 42, 0.7);
            color: var(--text);
            border-color: rgba(148, 163, 184, 0.2);
        }

        /* ── Global dark-mode text override ────────────────────────────── */
        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6,
        body.dark-mode p,
        body.dark-mode span:not(.badge):not(.brand-mark),
        body.dark-mode label,
        body.dark-mode .form-label,
        body.dark-mode .form-text,
        body.dark-mode .card-title,
        body.dark-mode .card-text,
        body.dark-mode .card-header,
        body.dark-mode .card-body,
        body.dark-mode .card-footer,
        body.dark-mode .modal-body,
        body.dark-mode .modal-header,
        body.dark-mode .modal-footer,
        body.dark-mode .modal-title,
        body.dark-mode .dropdown-item,
        body.dark-mode .dropdown-header,
        body.dark-mode .list-group-item,
        body.dark-mode .fw-bold,
        body.dark-mode .fw-semibold,
        body.dark-mode .small,
        body.dark-mode .text-dark,
        body.dark-mode .text-black,
        body.dark-mode .text-body,
        body.dark-mode .page-header h1,
        body.dark-mode .page-header h2,
        body.dark-mode .page-header h3,
        body.dark-mode .page-header p,
        body.dark-mode .topbar h4,
        body.dark-mode .topbar span,
        body.dark-mode th,
        body.dark-mode td {
            color: var(--text) !important;
        }

        body.dark-mode .text-muted,
        body.dark-mode .text-secondary {
            color: #94a3b8 !important;
        }

        body.dark-mode .dropdown-item:hover,
        body.dark-mode .dropdown-item:focus {
            background: rgba(148, 163, 184, 0.12);
            color: #ffffff !important;
        }

        body.dark-mode .dropdown-menu {
            background: #1e293b;
            border-color: rgba(148, 163, 184, 0.2);
        }

        body.dark-mode .list-group-item {
            background: transparent;
            border-color: rgba(148, 163, 184, 0.15);
        }

        body.dark-mode .modal-content {
            background: #1e293b;
            border-color: rgba(148, 163, 184, 0.2);
        }

        body.dark-mode .modal-header,
        body.dark-mode .modal-footer {
            border-color: rgba(148, 163, 184, 0.15);
        }

        /* ── Light mode: ensure dark text stays dark ────────────────────── */
        body:not(.dark-mode) .text-dark,
        body:not(.dark-mode) .text-black,
        body:not(.dark-mode) .text-body {
            color: #212529 !important;
        }



        a {
            text-decoration: none;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #f7f4ef, #f0ece5);
            border-right: 1px solid rgba(148, 163, 184, 0.2);
            padding: 24px 16px 20px;
            box-shadow: inset -1px 0 0 rgba(255,255,255,0.5);
            transition:
                margin-left 0.6s cubic-bezier(0.65, 0, 0.35, 1),
                opacity 0.6s ease-in-out,
                transform 0.6s cubic-bezier(0.65, 0, 0.35, 1),
                box-shadow 0.6s ease-in-out;
            flex-shrink: 0;
        }

        .brand-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 4px 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: 0.4px;
            color: #38352f;
            margin: 0;
        }

        .sidebar-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .sidebar-toggle-btn:hover {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: var(--primary);
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #d9c4a1, #b7a58a);
            box-shadow: 0 10px 18px rgba(183, 165, 138, 0.35);
            font-size: 1.1rem;
            color: #fff;
        }

        .nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #000000;
            padding: 10px 12px;
            margin: 0;
            user-select: none;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .nav-section-title:hover {
            background: rgba(100, 116, 139, 0.08);
            color: var(--text);
        }

        .nav-section-title .section-chevron {
            font-size: 0.7rem;
            transition: transform 0.25s ease;
        }

        .nav-section-title[aria-expanded="true"] .section-chevron {
            transform: rotate(180deg);
        }

        .nav-submenu {
            padding-left: 8px;
        }

        .sidebar .nav > .collapse,
        .sidebar .nav > .collapsing {
            flex-shrink: 0;
        }

        .sidebar .nav > .collapsing {
            overflow: hidden;
            transition: height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar .nav-section-title .section-chevron {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (prefers-reduced-motion: reduce) {
            .sidebar .nav > .collapsing,
            .sidebar .nav-section-title .section-chevron {
                transition: none;
            }
        }

        body.dark-mode .nav-section-title {
            color: #ffffff !important;
        }

        body.dark-mode .nav-section-title:hover {
            background: rgba(148, 163, 184, 0.1);
            color: #e2e8f0 !important;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--text);
            font-weight: 600;
            font-size: 0.94rem;
            transition: all 0.15s ease;
        }

        .nav-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            color: var(--muted);
            transition: color 0.15s ease;
        }

        .nav-link:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .nav-link:hover i {
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 700;
        }

        .nav-link.active i {
            color: var(--primary);
        }

        .main-content {
            flex: 1;
            padding: 28px 32px 36px;
            min-width: 0;
            transition: padding 0.25s ease;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1030;
            margin-bottom: 28px;
            padding: 18px 20px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
        }

        .topbar h4 {
            font-weight: 800;
            letter-spacing: 0.2px;
            color: #2e2b29;
        }

        /* ── Settings gear button & dropdown ───────────────────────────── */
        .settings-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .settings-toggle-btn:hover,
        .settings-toggle-btn[aria-expanded="true"] {
            background: var(--primary-soft);
            border-color: var(--primary);
            color: var(--primary);
        }

        .settings-dropdown-menu {
            min-width: 230px;
            padding: 6px 0;
            margin-top: 0 !important;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: #ffffff;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.14), 0 2px 8px rgba(0, 0, 0, 0.08);
            z-index: 1050;
        }

        .settings-dropdown-menu .dropdown-item {
            border-radius: 8px;
            margin: 2px 6px;
            width: calc(100% - 12px);
            font-size: 0.9rem;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .settings-dropdown-menu .dropdown-item:hover {
            background: rgba(148, 163, 184, 0.1);
        }

        body.dark-mode .settings-toggle-btn {
            background: rgba(30, 41, 59, 0.8);
            border-color: rgba(148, 163, 184, 0.25);
            color: #e2e8f0;
        }

        body.dark-mode .settings-toggle-btn:hover,
        body.dark-mode .settings-toggle-btn[aria-expanded="true"] {
            background: rgba(212, 185, 141, 0.12);
            border-color: var(--primary);
            color: var(--primary);
        }

        body.dark-mode .settings-dropdown-menu {
            background: #1e293b;
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4), 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        body.dark-mode .settings-dropdown-menu .dropdown-item {
            color: #e2e8f0;
        }

        body.dark-mode .settings-dropdown-menu .dropdown-item:hover {
            background: rgba(148, 163, 184, 0.12);
            color: #ffffff;
        }

        body.dark-mode .settings-dropdown-menu .dropdown-divider {
            border-color: rgba(148, 163, 184, 0.15);
        }
        .card {
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(243,239,232,0.9));
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 18px;
            color: #2d2d2d;
            box-shadow: var(--shadow);
        }

        .table-wrap {
            overflow: hidden;
            border-radius: 16px;
            border: 1px solid rgba(120, 92, 58, 0.2);
            background: linear-gradient(180deg, #f9f1e3, #f3e7d4);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        .table {
            color: #3f2f22;
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(153, 120, 80, 0.05);
            --bs-table-hover-bg: rgba(163, 126, 79, 0.12);
            --bs-table-border-color: rgba(120, 92, 58, 0.16);
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(180deg, #e9d7b5, #dcc19a);
            color: #2f2a25;
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.9rem 1rem;
            font-weight: 800;
        }

        .table th,
        .table td {
            border-color: rgba(120, 92, 58, 0.14);
            vertical-align: middle;
            padding: 0.9rem 1rem;
            color: #2d2d2d;
            font-weight: 600;
        }

        .table tbody tr {
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(188, 147, 90, 0.12);
        }

        .btn {
            border-radius: 12px;
            padding: 0.7rem 1.1rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ccb48b, #b7a58a);
            border-color: transparent;
            box-shadow: 0 12px 20px rgba(183, 165, 138, 0.25);
            color: #fff;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-color: transparent;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #c9c7c2, #a8a39a);
            border-color: transparent;
            color: #2d2d2d;
        }

        .btn-outline-light {
            border-color: rgba(130, 126, 118, 0.4);
            color: #3a3835;
        }

        .theme-toggle-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(130, 126, 118, 0.3);
            background: rgba(255, 255, 255, 0.5);
            color: #2d2d2d;
            border-radius: 12px;
            width: 44px;
            height: 44px;
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }

        .theme-toggle-button:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.82);
        }

        body.dark-mode .theme-toggle-button {
            background: rgba(15, 23, 42, 0.7);
            color: #f8fafc;
            border-color: rgba(148, 163, 184, 0.2);
        }

        .badge {
            font-size: 0.76rem;
            font-weight: 600;
            padding: 0.42rem 0.7rem;
            border-radius: 999px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(148, 163, 184, 0.32);
            color: #2d2d2d;
            border-radius: 12px;
            min-height: 46px;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.86);
            border-color: rgba(183, 165, 138, 0.9);
            color: #2d2d2d;
            box-shadow: 0 0 0 0.25rem rgba(183, 165, 138, 0.14);
        }

        .form-control::placeholder {
            color: var(--muted);
        }

        .alert {
            border-radius: 14px;
            border: none;
            box-shadow: var(--shadow);
        }

        .stats-card {
            padding: 22px 20px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.14), rgba(16, 185, 129, 0.08));
            box-shadow: var(--shadow);
            min-height: 120px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 22px 45px rgba(2, 6, 23, 0.55);
        }

        .stat-detail-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 72px;
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            background: rgba(183, 165, 138, 0.15);
            color: #5b4631;
            font-weight: 800;
            font-size: 1.1rem;
        }

        #statDetailInfo {
            display: block;
        }

        .stat-detail-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 0.5rem;
        }

        .stat-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.8rem;
            border-radius: 12px;
            background: rgba(183, 165, 138, 0.08);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .gradient-blue {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.26), rgba(96, 165, 250, 0.14));
        }

        .gradient-green {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.26), rgba(52, 211, 153, 0.12));
        }

        .gradient-amber {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.24), rgba(251, 191, 36, 0.12));
        }

        .gradient-red {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.22), rgba(248, 113, 113, 0.12));
        }

        .gradient-violet {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(196, 181, 253, 0.1));
        }

        .dashboard-hero {
            background: linear-gradient(135deg, rgba(59,130,246,0.22), rgba(16,185,129,0.1));
            border: 1px solid rgba(96,165,250,0.22);
        }

        .stock-warning-card {
            padding: 0.85rem 1.25rem;
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            border-left: 4px solid #f59e0b;
            border-radius: 12px;
            box-shadow: none;
        }

        .stock-warning-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .stock-warning-button {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border: 1px solid rgba(217, 119, 6, 0.35);
            background: #ffffff;
            color: #b45309;
            border-radius: 8px;
            padding: 0.45rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .stock-warning-button:hover {
            background: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }

        .eyebrow {
            margin: 0 0 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 0.76rem;
            color: var(--text);
        }

        .stats-card .text-muted {
            color: rgba(31, 41, 55, 0.82) !important;
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-weight: 700;
        }

        .stats-card h3 {
            font-size: 2rem;
            font-weight: 900;
            margin-top: 8px;
            margin-bottom: 0;
            color: #1f2937;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h3 {
            font-weight: 800;
            margin: 0;
        }

        .section-card {
            padding: 1.25rem;
        }

        .login-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at top, rgba(59, 130, 246, 0.18), transparent 25%),
                linear-gradient(135deg, #020817, #0f172a 35%, #111827);
        }

        .login-card {
            width: min(100%, 470px);
            padding: 2rem;
            border-radius: 24px;
            background: rgba(15, 23, 42, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 28px 55px rgba(2, 6, 23, 0.55);
            backdrop-filter: blur(8px);
        }

        .login-brand {
            margin-bottom: 1.2rem;
            text-align: center;
        }

        .login-brand .brand-mark {
            width: 56px;
            height: 56px;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .login-card h3 {
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .login-card .subtitle {
            color: var(--muted);
            margin-bottom: 1.5rem;
        }

        .login-card .hint {
            margin-top: 1rem;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(96, 165, 250, 0.15);
            color: #dbeafe;
            font-size: 0.9rem;
        }

        :root {
            --bg-primary: #f5f6f8;
            --bg-secondary: #ffffff;
            --surface: #ffffff;
            --surface-soft: #f8f9fa;
            --border: #dee2e6;
            --text: #212529;
            --muted: #6c757d;
            --primary: #0d6efd;
            --primary-soft: #e7f1ff;
            --success: #198754;
            --warning: #ffc107;
            --danger: #dc3545;
            --shadow: 0 1px 3px rgba(33, 37, 41, 0.08);
            --sidebar-bg: #f8f9fa;
            --topbar-bg: #ffffff;
            --card-bg: #ffffff;
            --table-head: #f1f3f5;
            --table-head-text: #212529;
            --input-bg: #ffffff;
        }

        body.dark-mode {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --surface: #1f2937;
            --surface-soft: #283548;
            --border: #374151;
            --text: #ffffff;
            --muted: #cbd5e1;
            --primary: #6ea8fe;
            --primary-soft: #26384f;
            --success: #75b798;
            --warning: #ffda6a;
            --danger: #ea868f;
            --shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
            --sidebar-bg: #182234;
            --topbar-bg: #1f2937;
            --card-bg: #1f2937;
            --table-head: #283548;
            --table-head-text: #ffffff;
            --input-bg: #182234;
        }

        body {
            background: var(--bg-primary);
            color: var(--text);
            transition: none;
        }

        .sidebar {
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            box-shadow: none;
        }

        .brand {
            color: var(--text);
            font-size: 1.35rem;
            letter-spacing: 0;
        }

        .brand-mark {
            border-radius: 6px;
            background: #0d6efd;
            box-shadow: none;
        }

        .nav-link,
        .nav-link:hover,
        .nav-link.active {
            border-radius: 6px;
            transition: none;
            transform: none;
            box-shadow: none;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .topbar {
            background: var(--topbar-bg);
            border-color: var(--border);
            border-radius: 8px;
            box-shadow: none;
            backdrop-filter: none;
        }

        .topbar h4 {
            color: var(--text);
            letter-spacing: 0;
        }

        .card,
        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: none;
            color: var(--text);
        }

        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 6px;
            box-shadow: none;
        }

        .table {
            color: var(--text);
            --bs-table-border-color: var(--border);
            --bs-table-striped-bg: rgba(108, 117, 125, 0.04);
            --bs-table-hover-bg: rgba(13, 110, 253, 0.05);
        }

        .table thead th {
            background: var(--table-head);
            color: var(--table-head-text);
            letter-spacing: 0;
        }

        .table th,
        .table td {
            color: var(--text);
            font-weight: 400;
        }

        .table tbody tr:hover {
            background: #f1f3f5;
        }

        body.dark-mode .table tbody tr:hover {
            background: #343a40;
        }

        .btn,
        .btn:hover {
            border-radius: 6px;
            box-shadow: none;
            transform: none;
            transition: none;
        }

        .btn-primary,
        .btn-primary:hover {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #ffffff;
        }

        .btn-secondary,
        .btn-secondary:hover {
            background: #6c757d;
            border-color: #6c757d;
            color: #ffffff;
        }

        .btn-danger,
        .btn-danger:hover {
            background: #dc3545;
            border-color: #dc3545;
        }

        .badge {
            border-radius: 4px;
        }

        .form-control,
        .form-select,
        .form-check-input {
            background-color: var(--input-bg);
            border-color: var(--border);
            border-radius: 6px;
            color: var(--text);
        }

        .form-control:focus,
        .form-select:focus {
            background-color: var(--input-bg);
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            color: var(--text);
        }

        .alert {
            border: 1px solid var(--border);
            border-radius: 6px;
            box-shadow: none;
        }

        .stats-card,
        .stats-card:hover {
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: none;
            transform: none;
            transition: none;
        }

        .gradient-blue,
        .gradient-green,
        .gradient-amber,
        .gradient-red,
        .gradient-violet {
            background: #eef1f4;
        }

        .dashboard-hero {
            background: #e7f1ff;
            border: 1px solid #b6d4fe;
            border-radius: 8px;
        }


        body.dark-mode {
            background: var(--bg-primary);
            color: #ffffff;
        }

        body.dark-mode,
        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6,
        body.dark-mode p,
        body.dark-mode span:not(.badge),
        body.dark-mode label,
        body.dark-mode .form-label,
        body.dark-mode .brand,
        body.dark-mode .nav-link,
        body.dark-mode .topbar h4,
        body.dark-mode .card,
        body.dark-mode .table,
        body.dark-mode .table th,
        body.dark-mode .table td,
        body.dark-mode .table thead th,
        body.dark-mode .modal-content,
        body.dark-mode .modal-title,
        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode .stats-card h3 {
            color: #ffffff !important;
        }

        body.dark-mode .text-secondary,
        body.dark-mode .text-muted,
        body.dark-mode .form-text,
        body.dark-mode small:not(.badge),
        body.dark-mode .eyebrow {
            color: #cbd5e1 !important;
        }

        body.dark-mode .sidebar,
        body.dark-mode .topbar,
        body.dark-mode .card,
        body.dark-mode .modal-content,
        body.dark-mode .table-wrap {
            background: var(--card-bg);
            border-color: var(--border);
            box-shadow: none;
        }

        body.dark-mode .nav-link {
            color: #e2e8f0 !important;
        }

        body.dark-mode .nav-link:hover,
        body.dark-mode .nav-link.active {
            background: #26384f !important;
            color: #ffffff !important;
        }

        body.dark-mode .table {
            color: #ffffff !important;
            --bs-table-color: #ffffff !important;
        }

        body.dark-mode .table thead th {
            background: var(--table-head) !important;
            color: var(--table-head-text) !important;
            border-bottom: 2px solid #374151 !important;
        }

        body.dark-mode .table th,
        body.dark-mode .table td {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        body.dark-mode .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.06) !important;
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select,
        body.dark-mode .form-check-input {
            background-color: var(--input-bg) !important;
            border-color: var(--border) !important;
            color: #ffffff !important;
        }

        body.dark-mode .form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }

        body.dark-mode .form-select option {
            background-color: #182234 !important;
            color: #ffffff !important;
        }

        body.dark-mode .btn-outline-secondary {
            color: #ffffff !important;
            border-color: #475569 !important;
            background-color: rgba(255, 255, 255, 0.04);
        }

        body.dark-mode .btn-outline-secondary:hover {
            background-color: #334155 !important;
            border-color: #94a3b8 !important;
            color: #ffffff !important;
        }

        body.dark-mode .btn-outline-light {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.35) !important;
            background-color: rgba(255, 255, 255, 0.06);
        }

        body.dark-mode .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.16) !important;
            border-color: #ffffff !important;
            color: #ffffff !important;
        }

        body.dark-mode .btn-secondary {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #ffffff !important;
        }

        body.dark-mode .btn-secondary:hover {
            background-color: #475569 !important;
            color: #ffffff !important;
        }

        body.dark-mode .pagination .page-link {
            background-color: var(--surface) !important;
            border-color: var(--border) !important;
            color: #ffffff !important;
        }

        body.dark-mode .pagination .page-item.active .page-link {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
        }

        body.dark-mode .pagination .page-item.disabled .page-link {
            background-color: var(--bg-primary) !important;
            border-color: var(--border) !important;
            color: #64748b !important;
        }

        body.dark-mode .dashboard-hero {
            background: #26384f;
            border-color: #3d5a80;
        }

        body.dark-mode .gradient-blue,
        body.dark-mode .gradient-green,
        body.dark-mode .gradient-amber,
        body.dark-mode .gradient-red,
        body.dark-mode .gradient-violet {
            background: #343a40;
        }

        body.dark-mode .stock-warning-card {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.22);
            border-left: 4px solid #f59e0b;
            box-shadow: none;
        }

        body.dark-mode .stock-warning-icon {
            background: rgba(245, 158, 11, 0.18);
            color: #fbbf24;
        }

        body.dark-mode .stock-warning-button {
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }

        body.dark-mode .stock-warning-button:hover {
            background: rgba(245, 158, 11, 0.22);
            color: #ffffff;
            border-color: #fbbf24;
        }

        /* ── Stats card & stat modal dark mode ──────────────────────────── */
        body.dark-mode .stats-card .text-muted,
        body.dark-mode .stats-card div.text-muted {
            color: #cbd5e1 !important;
        }

        body.dark-mode .stats-card h3 {
            color: #ffffff !important;
        }

        body.dark-mode .stat-detail-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
        }

        body.dark-mode #statInfoModal .modal-content {
            background: #1e293b !important;
            border-color: rgba(148, 163, 184, 0.2) !important;
        }

        body.dark-mode #statInfoModal h4,
        body.dark-mode #statInfoModal h5,
        body.dark-mode #statInfoModal .modal-title,
        body.dark-mode #statInfoModal .fw-bold {
            color: #ffffff !important;
        }

        body.dark-mode #statInfoModal p,
        body.dark-mode #statInfoModal .text-secondary {
            color: #94a3b8 !important;
        }

        body.dark-mode #statInfoModal .stat-detail-item {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(148, 163, 184, 0.15);
            color: #ffffff !important;
        }

        body.dark-mode #statInfoModal .stat-detail-item span,
        body.dark-mode #statInfoModal .stat-detail-item strong {
            color: #ffffff !important;
        }

        body.dark-mode #statInfoModal .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Sidebar collapse on desktop */
        @media (min-width: 992px) {
            .app-shell.sidebar-collapsed .sidebar {
                margin-left: -260px;
                opacity: 0;
                pointer-events: none;
            }
        }

        /* Sidebar responsive off-canvas on mobile/tablet */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1045;
                margin-left: -260px;
                height: 100vh;
                overflow-y: auto;
                box-shadow: none;
            }

            .app-shell.sidebar-open .sidebar {
                margin-left: 0;
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.25);
            }

            .sidebar-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                backdrop-filter: blur(2px);
                z-index: 1040;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity 0.6s ease-in-out, visibility 0s linear 0.6s;
            }

            .app-shell.sidebar-open .sidebar-backdrop {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
                transition: opacity 0.6s ease-in-out, visibility 0s;
            }

            .main-content {
                padding: 16px 14px 24px;
                width: 100%;
            }

            .topbar {
                padding: 12px 14px;
            }
        }

        /* Full width layout when no sidebar (Login page) */
        .app-shell.no-sidebar {
            display: block;
        }

        .no-sidebar .main-content,
        .main-content-full {
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        body.dark-mode .sidebar-toggle-btn {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text);
        }

        body.dark-mode .sidebar-toggle-btn:hover {
            background: rgba(212, 185, 141, 0.16);
            color: #fff;
            border-color: var(--primary);
        }

        .settings-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .settings-toggle-btn:hover,
        .settings-toggle-btn:focus,
        .settings-toggle-btn[aria-expanded="true"] {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: var(--primary);
        }

        .settings-dropdown-menu {
            min-width: 0;
            width: auto;
            position: static !important;
            order: -1;
            align-items: center;
            gap: 4px;
            border-radius: 8px;
            border: 0;
            background: transparent;
            box-shadow: none;
            padding: 0;
            margin: 0 !important;
        }

        .settings-dropdown-menu.show {
            display: flex;
        }

        .settings-dropdown-menu .dropdown-item {
            color: var(--text);
            font-size: 0.92rem;
            font-weight: 500;
            padding: 0.55rem 1rem;
            background: transparent;
            border: none;
            width: auto;
            margin: 0;
            min-height: 40px;
            white-space: nowrap;
            text-align: left;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .settings-dropdown-menu .dropdown-item:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .settings-dropdown-menu .dropdown-item.text-danger:hover {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger) !important;
        }

        body.dark-mode .settings-toggle-btn {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text);
        }

        body.dark-mode .settings-toggle-btn:hover,
        body.dark-mode .settings-toggle-btn:focus,
        body.dark-mode .settings-toggle-btn[aria-expanded="true"] {
            background: #26384f;
            color: #9ec5fe;
            border-color: var(--primary);
        }

        body.dark-mode .settings-dropdown-menu {
            background: transparent;
            border: 0;
            box-shadow: none;
        }

        body.dark-mode .settings-dropdown-menu .border-bottom {
            border-color: var(--border) !important;
        }

        body.dark-mode .settings-dropdown-menu .dropdown-divider {
            border-color: var(--border);
        }

        body.dark-mode .settings-dropdown-menu .dropdown-item {
            color: var(--text);
        }

        body.dark-mode .settings-dropdown-menu .dropdown-item:hover {
            background: #26384f;
            color: #9ec5fe;
        }

        body.dark-mode .settings-dropdown-menu .dropdown-item.text-danger {
            color: #ea868f !important;
        }

        body.dark-mode .settings-dropdown-menu .dropdown-item.text-danger:hover {
            background: rgba(234, 134, 143, 0.15);
            color: #f87171 !important;
        }

        .settings-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .settings-backdrop.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        @media (prefers-reduced-motion: reduce) {
            .settings-backdrop {
                transition: none;
            }
        }

        body.dark-mode .settings-backdrop {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .topbar {
            z-index: 1045;
        }

        .topbar .dropdown {
            position: relative;
            z-index: 1046;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .topbar-greeting {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        @media (max-width: 767.98px) {
            body.settings-open .topbar-greeting {
                display: none;
            }

            .settings-dropdown-menu .dropdown-item {
                padding: 0.5rem;
            }

            .topbar .dropdown,
            .settings-dropdown-menu {
                gap: 2px;
            }
        }

        @media (max-width: 399.98px) {
            .settings-dropdown-menu .dropdown-item span {
                display: none;
            }
        }

        body.settings-open .sidebar,
        body.settings-open .main-content > *:not(.topbar):not(.alert) {
            filter: blur(3px);
            transition: filter 0.25s ease;
        }

        .sidebar,
        .main-content > *:not(.topbar):not(.alert) {
            transition: filter 0.25s ease;
        }

        :root {
            --bg-primary: #f3f7f6;
            --bg-secondary: #eaf1f0;
            --surface: #ffffff;
            --surface-soft: #eff5f4;
            --border: rgba(20, 66, 75, 0.14);
            --text: #17343c;
            --muted: #647a80;
            --primary: #0e6670;
            --primary-soft: rgba(14, 102, 112, 0.1);
            --success: #287452;
            --warning: #b86f22;
            --danger: #a63d4d;
            --shadow: 0 12px 30px rgba(28, 62, 70, 0.08);
            --sidebar-bg: linear-gradient(180deg, #ffffff, #f0f5f4);
            --topbar-bg: rgba(255, 255, 255, 0.9);
            --card-bg: #ffffff;
            --table-head: #e5efee;
            --table-head-text: #17343c;
            --input-bg: #ffffff;
        }

        body.dark-mode {
            --bg-primary: #10232b;
            --bg-secondary: #17313a;
            --surface: #19333c;
            --surface-soft: #203d46;
            --border: rgba(184, 213, 214, 0.16);
            --text: #edf5f4;
            --muted: #b5c8ca;
            --primary: #7bc8c5;
            --primary-soft: rgba(123, 200, 197, 0.14);
            --success: #7ac5a0;
            --warning: #f1bd72;
            --danger: #ec919b;
            --shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
            --sidebar-bg: linear-gradient(180deg, #17323b, #11272f);
            --topbar-bg: rgba(25, 51, 60, 0.94);
            --card-bg: #19333c;
            --table-head: #24434c;
            --table-head-text: #edf5f4;
            --input-bg: #17313a;
        }

        body:not(.dark-mode) {
            background: linear-gradient(160deg, #f8fbfa 0%, var(--bg-primary) 62%, #edf4f3 100%);
            color: var(--text);
        }

        body.dark-mode {
            background: var(--bg-primary);
            color: var(--text);
        }

        .sidebar {
            background: var(--sidebar-bg);
            border-right-color: var(--border);
        }

        .brand {
            flex: 1 1 0;
            gap: 8px;
            color: var(--text);
            font-size: 0.95rem;
            letter-spacing: 0;
            min-width: 0;
            overflow: hidden;
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            border-radius: 8px;
            background: linear-gradient(145deg, #0d5264, #0e7074);
            box-shadow: 0 6px 14px rgba(14, 102, 112, 0.2);
            color: #ffffff;
        }

        .brand-mark {
            font-size: 0.64rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .brand-copy {
            display: grid;
            flex: 1 1 0;
            gap: 2px;
            min-width: 0;
            overflow: hidden;
        }

        .brand-name {
            font-size: 0.98rem;
            line-height: 1.15;
            font-weight: 800;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 0.61rem;
            line-height: 1.3;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            overflow-wrap: anywhere;
        }

        .brand-wrapper {
            gap: 8px;
            min-width: 0;
        }

        body,
        button,
        input,
        select,
        textarea {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
        }

        h1,
        h2,
        h3,
        .page-header h3,
        .dashboard-hero h3,
        .stats-card h3,
        .login-card h3,
        .brand-name,
        .brand-top-copy strong {
            font-family: "Montserrat", sans-serif;
            font-weight: 900;
        }

        h4,
        h5,
        h6,
        .topbar h4 {
            font-family: "Montserrat", sans-serif;
            font-weight: 500;
        }

        strong,
        b,
        .fw-bold {
            font-family: "Poppins", sans-serif;
            font-weight: 700;
        }

        .nav-link,
        .nav-section-title,
        .btn,
        .form-label {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
        }

        .brand-subtitle {
            font-family: "Poppins", sans-serif;
            font-weight: 500;
        }

        .nav-section-title {
            color: var(--muted);
        }

        .nav-link.active {
            border-left: 3px solid var(--primary);
            border-radius: 0 6px 6px 0;
            padding-left: 9px;
        }

        body.dark-mode .nav-link.active {
            background: var(--primary-soft) !important;
            color: var(--primary) !important;
        }

        .topbar {
            background: var(--topbar-bg);
            border-color: var(--border);
            border-radius: 8px;
            box-shadow: none;
        }

        .btn-primary {
            background: #0e6670;
            border-color: #0e6670;
            color: #ffffff;
        }

        .btn-primary:hover,
        .btn-primary:focus-visible,
        .btn-primary:active {
            background: #0a525b;
            border-color: #0a525b;
            color: #ffffff;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem var(--primary-soft);
        }

        .table thead th {
            background: var(--table-head);
            color: var(--table-head-text);
        }

        .dashboard-hero {
            position: relative;
            overflow: hidden;
            min-height: 168px;
            border: 0;
            border-radius: 8px;
            background: linear-gradient(112deg, #123f54 0%, #116a70 72%, #a83e4d 100%);
        }

        .dashboard-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: repeating-linear-gradient(135deg, rgba(255,255,255,0.07) 0 1px, transparent 1px 18px);
        }

        .dashboard-hero > * {
            position: relative;
            z-index: 1;
        }

        .dashboard-hero .eyebrow,
        .dashboard-hero h3,
        .dashboard-hero .dashboard-hero-copy {
            color: #ffffff;
        }

        .dashboard-hero .eyebrow {
            letter-spacing: 0.08em;
            font-weight: 700;
            opacity: 0.84;
        }

        .dashboard-hero .dashboard-hero-copy {
            max-width: 650px;
            margin: 0.55rem 0 0;
            color: rgba(255, 255, 255, 0.84);
            line-height: 1.55;
        }

        .dashboard-hero .btn-primary,
        .dashboard-hero .btn-primary:hover,
        .dashboard-hero .btn-primary:focus-visible {
            background: #ffffff;
            border-color: #ffffff;
            color: #154b58;
        }

        .dashboard-hero .btn-primary:hover,
        .dashboard-hero .btn-primary:focus-visible {
            background: #e8f3f1;
            border-color: #e8f3f1;
            color: #123f54;
        }

        .dashboard-hero .btn-outline-light:hover {
            color: #154b58;
        }

        .gradient-blue {
            background: #e7f0f4;
        }

        .gradient-green {
            background: #e5f2ee;
        }

        .gradient-amber {
            background: #fbf0df;
        }

        .gradient-red {
            background: #f7e9eb;
        }

        .gradient-violet {
            background: #eeeaf3;
        }

        body.dark-mode .gradient-blue,
        body.dark-mode .gradient-green,
        body.dark-mode .gradient-amber,
        body.dark-mode .gradient-red,
        body.dark-mode .gradient-violet {
            background: #203d46;
        }

        body.dark-mode .brand {
            color: var(--text);
        }

        body.dark-mode .brand-subtitle,
        body.dark-mode .nav-section-title {
            color: var(--muted) !important;
        }

        body.dark-mode .brand-mark {
            background: linear-gradient(145deg, #0d6573, #228083);
        }

        body.dark-mode .dashboard-hero {
            background: linear-gradient(112deg, #123744 0%, #145b61 72%, #743440 100%);
        }

        @media (max-width: 575.98px) {
            .dashboard-hero {
                min-height: 0;
                padding: 1.25rem !important;
            }

            .dashboard-hero .d-flex {
                align-items: flex-start !important;
            }
        }

        :root {
            --bg-primary: #f6f7fb;
            --bg-secondary: #ffffff;
            --surface: #ffffff;
            --surface-soft: #f7f8fc;
            --border: rgba(48, 54, 132, 0.14);
            --text: #252943;
            --muted: #69718a;
            --primary: #303684;
            --primary-soft: rgba(48, 54, 132, 0.09);
            --success: #287452;
            --warning: #a87300;
            --danger: #a63d4d;
            --shadow: 0 10px 28px rgba(39, 45, 99, 0.07);
            --sidebar-bg: linear-gradient(155deg, #303684 0%, #272d78 100%);
            --topbar-bg: rgba(255, 255, 255, 0.94);
            --card-bg: #ffffff;
            --table-head: #f0f1fa;
            --table-head-text: #303684;
            --input-bg: #ffffff;
        }

        body.dark-mode {
            --bg-primary: #171a2d;
            --bg-secondary: #1d2037;
            --surface: #222640;
            --surface-soft: #292d49;
            --border: rgba(190, 195, 239, 0.16);
            --text: #edf0ff;
            --muted: #b6bdd7;
            --primary: #aeb4ff;
            --primary-soft: rgba(174, 180, 255, 0.13);
            --success: #7ac5a0;
            --warning: #f1bd72;
            --danger: #ec919b;
            --shadow: 0 12px 30px rgba(0, 0, 0, 0.24);
            --sidebar-bg: linear-gradient(155deg, #242956 0%, #1b2048 100%);
            --topbar-bg: rgba(34, 38, 64, 0.96);
            --card-bg: #222640;
            --table-head: #2c3153;
            --table-head-text: #edf0ff;
            --input-bg: #1d2037;
        }

        body:not(.dark-mode) {
            background: #f6f7fb;
            color: var(--text);
        }

        .app-shell:not(.no-sidebar) {
            min-height: 100vh;
            background: var(--bg-primary);
        }

        .app-shell:not(.no-sidebar) .sidebar {
            position: relative;
            isolation: isolate;
            background:
                radial-gradient(ellipse at 14% 0%, rgba(112, 124, 255, 0.28), transparent 38%),
                linear-gradient(160deg, #343a8e 0%, #292f7d 48%, #202665 100%);
            border-right: 0;
            box-shadow: 8px 0 30px rgba(27, 32, 87, 0.2), inset -1px 0 rgba(255, 255, 255, 0.08);
        }

        .app-shell:not(.no-sidebar) .sidebar::before {
            content: "";
            position: absolute;
            z-index: 0;
            inset: 0;
            pointer-events: none;
            background: url("{{ asset('login-gradient-waves.svg') }}?v=3") center / 100% 100% no-repeat;
            opacity: 0.3;
        }

        .app-shell:not(.no-sidebar) .sidebar::after {
            content: "";
            position: absolute;
            z-index: 0;
            inset: 0;
            pointer-events: none;
            background: url("{{ asset('geometric-pattern.svg') }}") 0 0 / 64px 56px repeat;
            opacity: 0.1;
        }

        .app-shell:not(.no-sidebar) .sidebar > * {
            position: relative;
            z-index: 1;
        }

        .app-shell:not(.no-sidebar) .brand {
            color: #ffffff;
        }

        .app-shell:not(.no-sidebar) .brand-wrapper {
            position: relative;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
            margin: 0 0 19px;
            padding: 17px 30px 16px;
            border-bottom: 3px solid #f5c400;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 10px 24px rgba(13, 18, 67, 0.2);
        }

        .app-shell:not(.no-sidebar) .brand {
            flex: 0 1 auto;
            align-items: center;
            flex-direction: column;
            gap: 12px;
            color: #303684;
            text-align: center;
        }

        .app-shell:not(.no-sidebar) .brand-mark {
            position: relative;
            width: 120px;
            height: 120px;
            flex: 0 0 120px;
            overflow: hidden;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .app-shell:not(.no-sidebar) .brand-mark img {
            display: block;
            position: absolute;
            top: -8%;
            left: -11%;
            width: 365%;
            max-width: none;
            height: auto;
        }

        .app-shell:not(.no-sidebar) .brand-copy {
            flex: 0 1 auto;
            overflow: visible;
            text-align: center;
        }

        .app-shell:not(.no-sidebar) .brand-name {
            display: grid;
            gap: 0;
            color: #303684;
            font-size: 1rem;
            line-height: 1.18;
            letter-spacing: 0.015em;
            text-transform: uppercase;
        }

        .app-shell:not(.no-sidebar) .brand-subtitle {
            color: #303684;
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.07em;
        }

        .app-shell:not(.no-sidebar) .brand-copy {
            gap: 4px;
        }

        .app-shell:not(.no-sidebar) .nav {
            gap: 6px;
        }

        .app-shell:not(.no-sidebar) .nav-section-title {
            margin-top: 7px;
            padding: 9px 10px 7px;
            border: 1px solid #ffffff;
            border-radius: 9px;
            background: #ffffff;
            color: #303684;
            font-size: 0.7rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: 0.1em;
            text-shadow: none;
            box-shadow: 0 4px 12px rgba(15, 20, 72, 0.16);
        }

        .app-shell:not(.no-sidebar) .nav-section-title .section-chevron {
            color: #303684;
            font-size: 0.82rem;
            -webkit-text-stroke: 0.4px currentColor;
        }

        .app-shell:not(.no-sidebar) .nav-section-title:hover {
            background: #fff7db;
            color: #252a68;
        }

        .app-shell:not(.no-sidebar) .nav-submenu {
            padding-left: 0;
        }

        .app-shell:not(.no-sidebar) .nav-link {
            min-height: 42px;
            margin: 2px 0;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-left: 3px solid transparent;
            border-radius: 11px;
            background: transparent;
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            text-shadow: 0 1px 3px rgba(13, 18, 67, 0.55);
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .app-shell:not(.no-sidebar) .nav-link i {
            color: rgba(210, 216, 255, 0.88);
            font-size: 1.08rem;
        }

        .app-shell:not(.no-sidebar) .nav-link:hover {
            border-color: rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            transform: translateX(2px);
        }

        .app-shell:not(.no-sidebar) .nav-link.active {
            border-color: rgba(255, 255, 255, 0.12);
            border-left-color: #f5c400;
            background: linear-gradient(100deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.08));
            box-shadow: inset 0 1px rgba(255, 255, 255, 0.1), 0 7px 16px rgba(15, 20, 72, 0.16);
            color: #ffffff;
        }

        .app-shell:not(.no-sidebar) .nav-link:hover i,
        .app-shell:not(.no-sidebar) .nav-link.active i {
            color: #f5d65a;
        }

        .app-shell:not(.no-sidebar) .sidebar-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.42);
            color: #ffffff;
        }

        .app-shell:not(.no-sidebar) .nav-link:hover i,
        .app-shell:not(.no-sidebar) .nav-link.active i {
            color: #f5d65a;
        }

        .app-shell:not(.no-sidebar) .main-content {
            position: relative;
            min-height: 100vh;
            background: var(--bg-primary) url("{{ asset('geometric-pattern-light.svg') }}") 0 0 / 64px 56px repeat;
            padding: 28px clamp(18px, 3vw, 40px) 40px;
        }

        .app-shell:not(.no-sidebar) .topbar,
        .app-shell:not(.no-sidebar) .card,
        .app-shell:not(.no-sidebar) .modal-content {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            box-shadow: var(--shadow);
            color: var(--text);
        }

        .app-shell:not(.no-sidebar) .topbar {
            margin-bottom: 28px;
            padding: 16px 20px;
            border-bottom: 4px solid #f5c400;
            backdrop-filter: blur(12px);
        }

        .app-shell:not(.no-sidebar) .topbar h4,
        .app-shell:not(.no-sidebar) .page-header h3,
        .app-shell:not(.no-sidebar) .main-content > .d-flex > h4 {
            color: var(--primary);
        }

        .app-shell:not(.no-sidebar) .topbar-greeting h4 {
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .app-shell:not(.no-sidebar) .sidebar-toggle-btn,
        .app-shell:not(.no-sidebar) .settings-toggle-btn {
            border-color: var(--border);
            border-radius: 11px;
            background: var(--surface-soft);
            color: var(--primary);
        }

        .app-shell:not(.no-sidebar) .sidebar-toggle-btn {
            width: 48px;
            height: 48px;
            border: 1px solid rgba(48, 54, 132, 0.16);
            border-radius: 14px;
            background: #f8f9ff;
            color: #303684;
            box-shadow: 0 2px 6px rgba(48, 54, 132, 0.05), inset 0 -3px 0 #f5c400;
        }

        .app-shell:not(.no-sidebar) .settings-toggle-btn {
            box-shadow: inset 0 -3px 0 #f5c400;
        }

        .app-shell:not(.no-sidebar) .sidebar-toggle-btn i {
            font-size: 1.2rem;
            line-height: 1;
            -webkit-text-stroke: 0.4px currentColor;
        }

        .app-shell:not(.no-sidebar) .sidebar-toggle-btn:hover,
        .app-shell:not(.no-sidebar) .sidebar-toggle-btn:focus-visible,
        .app-shell:not(.no-sidebar) .sidebar-toggle-btn:active {
            border-color: rgba(48, 54, 132, 0.38);
            background: #ffffff;
            color: #303684;
            box-shadow: 0 0 0 3px rgba(48, 54, 132, 0.14), inset 0 -3px 0 #f5c400;
        }

        .app-shell:not(.no-sidebar) .table-wrap {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            box-shadow: none;
        }

        .app-shell:not(.no-sidebar) .table-wrap.table-responsive {
            overflow-x: auto;
        }

        .app-shell:not(.no-sidebar) .table {
            color: var(--text);
            --bs-table-color: var(--text);
            --bs-table-bg: transparent;
            --bs-table-border-color: var(--border);
            --bs-table-striped-bg: rgba(48, 54, 132, 0.025);
            --bs-table-hover-bg: rgba(48, 54, 132, 0.055);
        }

        .app-shell:not(.no-sidebar) .table thead th {
            background: var(--table-head);
            color: var(--table-head-text);
            border-bottom: 1px solid var(--border);
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.045em;
            padding: 0.85rem 1rem;
        }

        .app-shell:not(.no-sidebar) .table th,
        .app-shell:not(.no-sidebar) .table td {
            border-color: var(--border);
            color: var(--text);
            padding: 0.8rem 1rem;
        }

        .app-shell:not(.no-sidebar) .table tbody tr:hover {
            background: rgba(48, 54, 132, 0.045);
        }

        .app-shell:not(.no-sidebar) .btn {
            border-radius: 10px;
            font-weight: 600;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }

        .app-shell:not(.no-sidebar) .btn:hover {
            transform: translateY(-1px);
        }

        .app-shell:not(.no-sidebar) .btn-primary,
        .app-shell:not(.no-sidebar) .btn-primary:hover,
        .app-shell:not(.no-sidebar) .btn-primary:focus-visible,
        .app-shell:not(.no-sidebar) .btn-primary:active {
            border-color: var(--primary);
            border-bottom: 3px solid #f5c400;
            background: var(--primary);
            color: #ffffff;
        }

        .app-shell:not(.no-sidebar) .btn-primary:hover,
        .app-shell:not(.no-sidebar) .btn-primary:focus-visible {
            background: #272d78;
            border-color: #272d78;
            border-bottom-color: #f5c400;
        }

        .app-shell:not(.no-sidebar) .btn-secondary,
        .app-shell:not(.no-sidebar) .btn-secondary:hover {
            border-color: #e5e7f2;
            background: #eef0f8;
            color: #373d67;
        }

        .app-shell:not(.no-sidebar) .btn-outline-secondary {
            border-color: rgba(48, 54, 132, 0.24);
            color: var(--primary);
        }

        .app-shell:not(.no-sidebar) .btn-outline-secondary:hover {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .app-shell:not(.no-sidebar) .form-control,
        .app-shell:not(.no-sidebar) .form-select {
            min-height: 44px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background-color: var(--input-bg);
            color: var(--text);
        }

        .app-shell:not(.no-sidebar) .form-control:focus,
        .app-shell:not(.no-sidebar) .form-select:focus {
            border-color: #777dc0;
            background-color: var(--input-bg);
            box-shadow: 0 0 0 0.2rem var(--primary-soft);
            color: var(--text);
        }

        .app-shell:not(.no-sidebar) .form-label {
            color: var(--primary);
            font-weight: 600;
        }

        .app-shell:not(.no-sidebar) .page-header {
            gap: 16px;
        }

        .app-shell:not(.no-sidebar) .stats-card {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-bottom: 4px solid #f5c400;
            border-radius: 16px;
            background: linear-gradient(135deg, #252a68 0%, #303684 52%, #454bac 100%);
            box-shadow: 0 10px 24px rgba(39, 45, 99, 0.16);
            color: #ffffff !important;
        }

        .app-shell:not(.no-sidebar) .stats-card .text-muted,
        .app-shell:not(.no-sidebar) .stats-card h3 {
            color: #ffffff !important;
        }

        .app-shell:not(.no-sidebar) .stats-card:hover {
            box-shadow: 0 14px 30px rgba(39, 45, 99, 0.24);
            transform: translateY(-2px);
        }

        .app-shell:not(.no-sidebar) .stats-card:focus-visible {
            outline: 3px solid rgba(245, 196, 0, 0.55);
            outline-offset: 3px;
        }

        .app-shell:not(.no-sidebar) .dashboard-hero {
            border: 0;
            border-radius: 18px;
            background: linear-gradient(125deg, #303684 0%, #383d91 72%, #454b9d 100%);
            box-shadow: 0 14px 32px rgba(39, 45, 99, 0.16);
        }

        .app-shell:not(.no-sidebar) .dashboard-hero::before {
            content: "";
            position: absolute;
            z-index: 0;
            inset: 0;
            pointer-events: none;
            background: url("{{ asset('login-gradient-waves.svg') }}?v=3") center / 100% 100% no-repeat;
            opacity: 0.3;
        }

        .app-shell:not(.no-sidebar) .dashboard-hero .eyebrow {
            color: #f5d65a;
            opacity: 1;
        }

        .app-shell:not(.no-sidebar) .dashboard-hero h3 {
            color: #ffffff;
        }

        .app-shell:not(.no-sidebar) .dashboard-hero .dashboard-hero-copy {
            color: #e4e8ff;
        }

        .app-shell:not(.no-sidebar) .dashboard-hero-centered .dashboard-hero-copy {
            margin-left: auto;
            margin-right: auto;
        }

        .app-shell:not(.no-sidebar) .dashboard-hero::after {
            background-color: rgba(255, 255, 255, 0.12);
            background-image: none;
            -webkit-mask: url("{{ asset('geometric-pattern.svg') }}") 0 0 / 64px 56px repeat;
            mask: url("{{ asset('geometric-pattern.svg') }}") 0 0 / 64px 56px repeat;
        }

        .app-shell:not(.no-sidebar) .dashboard-hero .btn-primary,
        .app-shell:not(.no-sidebar) .dashboard-hero .btn-primary:hover {
            border: 1px solid rgba(255, 255, 255, 0.86);
            background: #ffffff;
            color: var(--primary);
        }

        .app-shell:not(.no-sidebar) .dashboard-hero .btn-outline-light {
            border: 1px solid #ffffff;
            background: #ffffff;
            color: #303684;
            box-shadow: 0 5px 14px rgba(19, 24, 76, 0.18);
        }

        .app-shell:not(.no-sidebar) .dashboard-hero .btn-outline-light:hover,
        .app-shell:not(.no-sidebar) .dashboard-hero .btn-outline-light:focus-visible {
            border-color: #f5d65a;
            background: #fff7db;
            color: #252a68;
        }

        .app-shell:not(.no-sidebar) .alert {
            border-radius: 12px;
        }

        .app-shell:not(.no-sidebar) .pagination .page-link {
            border-color: var(--border);
            color: var(--primary);
        }

        .app-shell:not(.no-sidebar) .pagination .page-item.active .page-link {
            border-color: var(--primary);
            background: var(--primary);
            color: #ffffff;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .sidebar {
            background: var(--sidebar-bg);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .main-content {
            background-color: var(--bg-primary);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .brand-mark {
            border: 0;
            background: transparent;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .brand-wrapper {
            background: rgba(255, 255, 255, 0.96);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .brand,
        body.dark-mode .app-shell:not(.no-sidebar) .brand-name,
        body.dark-mode .app-shell:not(.no-sidebar) .brand-subtitle {
            color: #303684 !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .nav-link:hover i,
        body.dark-mode .app-shell:not(.no-sidebar) .nav-link.active i {
            color: #f5d65a !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .nav-section-title,
        body.dark-mode .app-shell:not(.no-sidebar) .nav-link {
            color: #ffffff !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .nav-section-title,
        body.dark-mode .app-shell:not(.no-sidebar) .nav-link,
        body.dark-mode .app-shell:not(.no-sidebar) .nav-link i {
            color: rgba(242, 243, 255, 0.82) !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .nav-section-title {
            border-color: #ffffff;
            background: #ffffff;
            color: #303684 !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .nav-section-title:hover {
            background: #fff7db;
            color: #252a68 !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .nav-link:hover,
        body.dark-mode .app-shell:not(.no-sidebar) .nav-link.active {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .nav-link:hover i,
        body.dark-mode .app-shell:not(.no-sidebar) .nav-link.active i {
            color: #f5d65a !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .topbar,
        body.dark-mode .app-shell:not(.no-sidebar) .card,
        body.dark-mode .app-shell:not(.no-sidebar) .modal-content,
        body.dark-mode .app-shell:not(.no-sidebar) .table-wrap {
            border-color: var(--border);
            background-color: var(--surface);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .topbar {
            border-bottom-color: #f5c400;
            color: var(--text);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .table,
        body.dark-mode .app-shell:not(.no-sidebar) .table th,
        body.dark-mode .app-shell:not(.no-sidebar) .table td {
            color: var(--text) !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .table tbody tr:hover {
            background: rgba(174, 180, 255, 0.08) !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .btn-secondary,
        body.dark-mode .app-shell:not(.no-sidebar) .btn-secondary:hover {
            border-color: var(--border);
            background: #363b5d;
            color: var(--text);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .form-control,
        body.dark-mode .app-shell:not(.no-sidebar) .form-select {
            border-color: var(--border);
            background-color: var(--input-bg);
            color: var(--text);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .form-control::placeholder {
            color: var(--muted);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .form-select option {
            background: var(--input-bg);
            color: var(--text);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .stats-card {
            background: linear-gradient(135deg, #252a68 0%, #303684 52%, #454bac 100%);
        }

        body.dark-mode .app-shell:not(.no-sidebar) .stats-card .text-muted,
        body.dark-mode .app-shell:not(.no-sidebar) .stats-card h3 {
            color: #ffffff !important;
        }

        body.dark-mode .app-shell:not(.no-sidebar) .dashboard-hero {
            background: linear-gradient(125deg, #252a62 0%, #303684 72%, #3b418c 100%);
        }

        @media (max-width: 991.98px) {
            .app-shell:not(.no-sidebar) .main-content {
                padding: 18px 16px 30px;
            }

            .app-shell:not(.no-sidebar) .topbar {
                margin-bottom: 22px;
            }
        }

        @media (max-width: 575.98px) {
            .app-shell:not(.no-sidebar) .main-content {
                padding: 14px 12px 24px;
            }

            .app-shell:not(.no-sidebar) .topbar {
                gap: 10px;
                padding: 12px;
                border-radius: 14px;
            }

            .app-shell:not(.no-sidebar) .topbar h4 {
                font-size: 1rem;
            }

            .app-shell:not(.no-sidebar) .card {
                border-radius: 14px;
            }

            .app-shell:not(.no-sidebar) .table th,
            .app-shell:not(.no-sidebar) .table td {
                padding: 0.65rem 0.75rem;
            }
        }

        @media (max-width: 991.98px) {
            .app-shell:not(.no-sidebar) .sidebar {
                position: fixed;
                margin-left: 0;
                transform: translateX(-100%);
                transition: transform 0.6s cubic-bezier(0.65, 0, 0.35, 1), box-shadow 0.6s ease-in-out;
            }

            .app-shell:not(.no-sidebar).sidebar-open .sidebar {
                transform: translateX(0);
                box-shadow: 0 0 25px rgba(0, 0, 0, 0.25);
            }
        }

        .searchable-select {
            position: relative;
            width: 100%;
        }

        .searchable-select-native {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        .searchable-select-toggle {
            min-height: calc(1.5em + 1rem + 2px);
            padding-right: 2.5rem;
        }

        .searchable-select-menu {
            position: absolute;
            z-index: 1060;
            top: calc(100% + .25rem);
            right: 0;
            left: 0;
            padding: .4rem;
            border: 1px solid var(--border, #dee2e6);
            border-radius: 8px;
            background: var(--card-bg, #fff);
            color: var(--text);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12);
        }

        .searchable-select-options {
            max-height: 260px;
            overflow-y: auto;
        }

        .searchable-select-option {
            display: block;
            width: 100%;
            padding: .55rem .7rem;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: inherit;
            text-align: left;
            overflow-wrap: anywhere;
        }

        .searchable-select-option:hover,
        .searchable-select-option.is-active {
            outline: none;
            background: var(--primary-soft);
        }

        .searchable-select-option[aria-selected="true"] {
            border-left: 3px solid var(--primary);
            font-weight: 600;
        }

        .searchable-select-option:disabled {
            opacity: .6;
        }

        .searchable-select-empty {
            padding: .5rem .7rem;
            color: var(--muted);
            font-size: .875rem;
        }
    </style>
</head>
<body>
    <div class="app-shell {{ !auth()->check() ? 'no-sidebar' : '' }}">
        @auth
            <aside class="sidebar" id="appSidebar">
                <div class="brand-wrapper">
                    <div class="brand">
                        <span class="brand-mark" aria-hidden="true"><img src="{{ asset('ums-logo-color.webp') }}" alt=""></span>
                        <span class="brand-copy">
                            <span class="brand-name"><span>Perpustakaan</span><span>UMS</span></span>
                            <span class="brand-subtitle">Inventaris &amp; Aset</span>
                        </span>
                    </div>
                </div>
                <nav class="nav">
                    @php
                        $menuUtamaActive = request()->routeIs('dashboard', 'transactions.*', 'guide');
                        $inventarisActive = request()->routeIs('items.*', 'categories.*', 'locations.*');
                        $aksesActive = request()->routeIs('staff.*', 'users.*');
                    @endphp

                    {{-- MENU UTAMA --}}
                    <button class="nav-section-title" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama" aria-expanded="{{ $menuUtamaActive ? 'true' : 'false' }}" aria-controls="menuUtama">
                        <span>Menu Utama</span>
                        <i class="bi bi-chevron-down section-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="collapse {{ $menuUtamaActive ? 'show' : '' }}" id="menuUtama">
                        <div class="nav-submenu">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2" aria-hidden="true"></i><span>Dashboard</span></a>
                            <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}"><i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Transaksi</span></a>
                            <a class="nav-link {{ request()->routeIs('guide') ? 'active' : '' }}" href="{{ route('guide') }}"><i class="bi bi-book" aria-hidden="true"></i><span>Panduan Penggunaan</span></a>
                        </div>
                    </div>

                    {{-- INVENTARIS & ASET --}}
                    <button class="nav-section-title" type="button" data-bs-toggle="collapse" data-bs-target="#menuInventaris" aria-expanded="{{ $inventarisActive ? 'true' : 'false' }}" aria-controls="menuInventaris">
                        <span>Inventaris &amp; Aset</span>
                        <i class="bi bi-chevron-down section-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="collapse {{ $inventarisActive ? 'show' : '' }}" id="menuInventaris">
                        <div class="nav-submenu">
                            <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}"><i class="bi bi-box-seam" aria-hidden="true"></i><span>Data Barang</span></a>
                            @if(auth()->user()->role === 'admin')
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tags" aria-hidden="true"></i><span>Kategori</span></a>
                                <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}"><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Lokasi</span></a>
                            @endif
                        </div>
                    </div>

                    {{-- PENGATURAN AKSES (Admin Only) --}}
                    @if(auth()->user()->role === 'admin')
                        <button class="nav-section-title" type="button" data-bs-toggle="collapse" data-bs-target="#menuAkses" aria-expanded="{{ $aksesActive ? 'true' : 'false' }}" aria-controls="menuAkses">
                            <span>Pengaturan Akses</span>
                            <i class="bi bi-chevron-down section-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="collapse {{ $aksesActive ? 'show' : '' }}" id="menuAkses">
                            <div class="nav-submenu">
                                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}"><i class="bi bi-person-badge" aria-hidden="true"></i><span>Data Staf</span></a>
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people" aria-hidden="true"></i><span>Pengguna</span></a>
                            </div>
                        </div>
                    @endif
                </nav>
            </aside>
            <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
            <div class="settings-backdrop" id="settingsBackdrop"></div>
        @endauth

        <main class="main-content {{ !auth()->check() ? 'main-content-full p-0' : '' }}">
            @auth
                <div class="topbar">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Buka atau tutup sidebar" title="Buka/Tutup Sidebar">
                            <i class="bi bi-list fs-5" aria-hidden="true"></i>
                        </button>
                        <div class="topbar-greeting">
                            <h4 class="mb-0">Halo, {{ auth()->user()->name }}</h4>
                            <small class="text-secondary">Inventaris Perpustakaan UMS · {{ ucfirst(auth()->user()->role) }}</small>
                        </div>
                    </div>
                    <div class="dropdown dropstart">
                        <button type="button" class="settings-toggle-btn" id="settingsDropdown" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false" aria-label="Pengaturan" title="Pengaturan">
                            <i class="bi bi-gear-fill fs-5" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end settings-dropdown-menu" aria-labelledby="settingsDropdown">
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" aria-label="Logout" title="Logout">
                                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endauth

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 border-0 shadow-sm" role="alert" id="globalSuccessAlert">
                    <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                    <div class="flex-grow-1 small fw-medium">{{ session('success') }}</div>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <style>
        .app-shell :is(button, .btn, [role="button"]) {
            transition: translate 0.15s ease, scale 0.1s ease, outline-color 0.15s ease,
                background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease,
                box-shadow 0.15s ease;
        }

        .app-shell :is(button, .btn, [role="button"]):not(:disabled):not([aria-disabled="true"]):hover {
            translate: 0 -1px;
        }

        .app-shell :is(button, .btn, [role="button"]):not(:disabled):not([aria-disabled="true"]):active {
            translate: 0 0;
            scale: 0.98;
        }

        .app-shell :is(button, .btn, [role="button"]):focus-visible {
            outline: 3px solid rgba(48, 54, 132, 0.32);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            .app-shell:not(.no-sidebar) .sidebar,
            .app-shell:not(.no-sidebar) .sidebar-backdrop {
                transition: none;
            }

            .app-shell :is(button, .btn, [role="button"]) {
                transition: none;
            }

            .app-shell :is(button, .btn, [role="button"]):hover,
            .app-shell :is(button, .btn, [role="button"]):active {
                translate: none;
                scale: none;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/searchable-select.js') }}"></script>
    <script>
        // Auto-dismiss alert sukses setelah 4.5 detik
        const globalSuccessAlert = document.getElementById('globalSuccessAlert');
        if (globalSuccessAlert) {
            setTimeout(() => {
                const alertInstance = bootstrap.Alert.getOrCreateInstance(globalSuccessAlert);
                alertInstance?.close();
            }, 4500);
        }

        window.history.scrollRestoration = 'manual';
        window.addEventListener('load', function () {
            window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
        });

        const settingsDropdownBtn = document.getElementById('settingsDropdown');
        const settingsBackdrop = document.getElementById('settingsBackdrop');

        if (settingsDropdownBtn && settingsBackdrop) {
            const dropdownEl = settingsDropdownBtn.closest('.dropdown');
            const settingsMenu = dropdownEl.querySelector('.settings-dropdown-menu');
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            let settingsAnimation = null;
            let closingSettings = false;
            let finishSettingsHide = false;
            const animateSettings = (opening) => {
                settingsAnimation?.cancel();
                if (reducedMotion.matches) return null;
                settingsAnimation = settingsMenu.animate([
                    { translate: opening ? '12px 0' : '0 0', opacity: opening ? 0 : 1 },
                    { translate: opening ? '0 0' : '12px 0', opacity: opening ? 1 : 0 },
                ], { duration: 220, easing: 'ease-out', fill: 'both' });
                return settingsAnimation;
            };

            if (dropdownEl) {
                dropdownEl.addEventListener('show.bs.dropdown', function () {
                    settingsBackdrop.classList.add('show');
                    document.body.classList.add('settings-open');
                });

                dropdownEl.addEventListener('shown.bs.dropdown', function () {
                    animateSettings(true);
                });

                dropdownEl.addEventListener('hide.bs.dropdown', function (event) {
                    if (finishSettingsHide || reducedMotion.matches) return;
                    event.preventDefault();
                    if (closingSettings) return;
                    closingSettings = true;
                    const animation = animateSettings(false);
                    animation.finished.then(() => {
                        finishSettingsHide = true;
                        bootstrap.Dropdown.getInstance(settingsDropdownBtn)?.hide();
                        finishSettingsHide = false;
                    }).catch(() => {
                        closingSettings = false;
                    });
                });

                dropdownEl.addEventListener('hidden.bs.dropdown', function () {
                    settingsAnimation?.cancel();
                    settingsAnimation = null;
                    closingSettings = false;
                    settingsBackdrop.classList.remove('show');
                    document.body.classList.remove('settings-open');
                });
            }


            settingsBackdrop.addEventListener('click', function () {
                const dropdownInstance = bootstrap.Dropdown.getInstance(settingsDropdownBtn);
                if (dropdownInstance) {
                    dropdownInstance.hide();
                }
            });
        }

        const statModalElement = document.getElementById('statInfoModal');
        const statModal = statModalElement ? new bootstrap.Modal(statModalElement) : null;
        const statBadge = document.getElementById('statDetailBadge');
        const statTitle = document.getElementById('statDetailTitle');
        const statDescription = document.getElementById('statDetailDescription');
        const statInfo = document.getElementById('statDetailInfo');

        document.querySelectorAll('[data-stat]').forEach((card) => {
            const openModal = () => {
                if (!statModal || !statBadge || !statTitle || !statDescription || !statInfo) return;

                const items = JSON.parse(card.dataset.items || '[]');
                statBadge.textContent = card.dataset.count;
                statTitle.textContent = card.dataset.title;
                statDescription.textContent = card.dataset.description;

                if (items.length) {
                    const isDark = document.body.classList.contains('dark-mode');
                    const badgeStyle = isDark
                        ? 'background:rgba(255,255,255,0.12);color:#ffffff;border:1px solid rgba(255,255,255,0.2);border-radius:6px;padding:2px 8px;font-size:0.78rem;font-weight:600;white-space:nowrap;'
                        : 'background:#f1f5f9;color:#1e293b;border:1px solid rgba(0,0,0,0.12);border-radius:6px;padding:2px 8px;font-size:0.78rem;font-weight:600;white-space:nowrap;';
                    const alertStyle = isDark
                        ? 'background:rgba(255,255,255,0.06);border:1px solid rgba(148,163,184,0.2);border-radius:10px;padding:0.75rem 1rem;color:#cbd5e1;margin-bottom:0.75rem;font-size:0.88rem;'
                        : 'background:#f8f9fa;border:1px solid rgba(0,0,0,0.1);border-radius:10px;padding:0.75rem 1rem;color:#374151;margin-bottom:0.75rem;font-size:0.88rem;';

                    const itemList = items.map((item) => {
                        const unit = item.unit || 'pcs';
                        return `
                            <li class="stat-detail-item">
                                <span>${item.name}</span>
                                <span style="${badgeStyle}">${item.quantity} ${unit}</span>
                            </li>
                        `;
                    }).join('');

                    statInfo.innerHTML = `
                        <div style="${alertStyle}">${card.dataset.detail}</div>
                        <div class="small text-uppercase text-muted mb-2">Daftar barang</div>
                        <ul class="stat-detail-list">${itemList}</ul>
                    `;
                } else {
                    const isDark = document.body.classList.contains('dark-mode');
                    const alertStyle = isDark
                        ? 'background:rgba(255,255,255,0.06);border:1px solid rgba(148,163,184,0.2);border-radius:10px;padding:0.75rem 1rem;color:#cbd5e1;margin-bottom:0;font-size:0.88rem;'
                        : 'background:#f8f9fa;border:1px solid rgba(0,0,0,0.1);border-radius:10px;padding:0.75rem 1rem;color:#374151;margin-bottom:0;font-size:0.88rem;';
                    statInfo.innerHTML = `
                        <div style="${alertStyle}">${card.dataset.detail}</div>
                    `;
                }


                statModal.show();
            };

            card.addEventListener('click', openModal);
            card.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openModal();
                }
            });
        });
        const appShell = document.querySelector('.app-shell');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        const isMobileScreen = () => window.innerWidth < 992;

        const toggleSidebar = () => {
            if (!appShell) return;
            if (isMobileScreen()) {
                appShell.classList.toggle('sidebar-open');
            } else {
                appShell.classList.toggle('sidebar-collapsed');
                localStorage.setItem('inventory-sidebar-collapsed', appShell.classList.contains('sidebar-collapsed') ? 'true' : 'false');
            }
        };

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', () => {
                if (appShell) appShell.classList.remove('sidebar-open');
            });
        }

        // Restore desktop collapsed state
        if (!isMobileScreen() && localStorage.getItem('inventory-sidebar-collapsed') === 'true') {
            if (appShell) appShell.classList.add('sidebar-collapsed');
        }

        // Close mobile sidebar on nav item click
        if (appShell) {
            appShell.querySelectorAll('.sidebar .nav-link:not(.info-toggle)').forEach((link) => {
                link.addEventListener('click', () => {
                    if (isMobileScreen()) {
                        appShell.classList.remove('sidebar-open');
                    }
                });
            });
        }

        window.addEventListener('resize', () => {
            if (!appShell) return;
            if (!isMobileScreen()) {
                appShell.classList.remove('sidebar-open');
            }
        });
    </script>
</body>
</html>
