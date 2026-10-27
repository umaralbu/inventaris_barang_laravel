<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventaris Barang')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
            transition: margin-left 0.25s ease, opacity 0.25s ease;
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

        .sidebar-collapse-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sidebar-collapse-btn:hover {
            background: var(--primary-soft);
            color: var(--text);
            border-color: var(--primary);
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
            margin-top: 8px !important;
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

        body.dark-mode #lowStockModal .modal-content {
            background: #1e293b !important;
            border-color: rgba(148, 163, 184, 0.2) !important;
        }

        body.dark-mode #lowStockModal h5,
        body.dark-mode #lowStockModal .modal-title {
            color: #ffffff !important;
        }

        body.dark-mode #lowStockModal .btn-close {
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
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                backdrop-filter: blur(2px);
                z-index: 1040;
                opacity: 0;
                transition: opacity 0.25s ease;
            }

            .app-shell.sidebar-open .sidebar-backdrop {
                display: block;
                opacity: 1;
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

        body.dark-mode .sidebar-collapse-btn {
            border-color: var(--border);
            color: var(--muted);
        }

        body.dark-mode .sidebar-collapse-btn:hover {
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
            min-width: 220px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            padding: 0.35rem 0;
            margin-top: 8px !important;
        }

        .settings-dropdown-menu .dropdown-item {
            color: var(--text);
            font-size: 0.92rem;
            font-weight: 500;
            padding: 0.55rem 1rem;
            background: transparent;
            border: none;
            width: 100%;
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
            background: var(--card-bg);
            border-color: var(--border);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
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
    </style>
</head>
<body>
    <div class="app-shell {{ !auth()->check() ? 'no-sidebar' : '' }}">
        @auth
            <aside class="sidebar" id="appSidebar">
                <div class="brand-wrapper">
                    <div class="brand">
                        <span class="brand-mark">I</span>
                        <span>Inventaris</span>
                    </div>
                    <button type="button" class="sidebar-collapse-btn" id="sidebarCloseBtn" aria-label="Tutup sidebar" title="Tutup sidebar">
                        <i class="bi bi-chevron-left" aria-hidden="true"></i>
                    </button>
                </div>
                <nav class="nav">
                    @php
                        $menuUtamaActive = request()->routeIs('dashboard', 'transactions.*');
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
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">Transaksi</a>
                        </div>
                    </div>

                    {{-- INVENTARIS & ASET --}}
                    <button class="nav-section-title" type="button" data-bs-toggle="collapse" data-bs-target="#menuInventaris" aria-expanded="{{ $inventarisActive ? 'true' : 'false' }}" aria-controls="menuInventaris">
                        <span>Inventaris &amp; Aset</span>
                        <i class="bi bi-chevron-down section-chevron" aria-hidden="true"></i>
                    </button>
                    <div class="collapse {{ $inventarisActive ? 'show' : '' }}" id="menuInventaris">
                        <div class="nav-submenu">
                            <a class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}" href="{{ route('items.index') }}">Data Barang</a>
                            @if(auth()->user()->role === 'admin')
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Kategori</a>
                                <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}">Lokasi</a>
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
                                <a class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}" href="{{ route('staff.index') }}">Data Staf</a>
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Pengguna</a>
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
                        <div>
                            <h4 class="mb-0">Halo, {{ auth()->user()->name }}</h4>
                            <small class="text-secondary">Role: {{ ucfirst(auth()->user()->role) }}</small>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="settings-toggle-btn" id="settingsDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Pengaturan" title="Pengaturan">
                            <i class="bi bi-gear-fill fs-5" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end settings-dropdown-menu" aria-labelledby="settingsDropdown">
                            <li class="px-3 pt-2 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                <i class="bi bi-sliders2 me-1"></i> Pengaturan
                            </li>
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small text-truncate">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Role: {{ ucfirst(auth()->user()->role) }}</div>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2" id="themeToggle">
                                    <i class="bi bi-toggle-off text-secondary" id="themeToggleIcon" aria-hidden="true"></i>
                                    <span id="themeToggleText">Mode Gelap</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
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
                <div class="alert alert-success">{{ session('success') }}</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.history.scrollRestoration = 'manual';
        window.addEventListener('load', function () {
            window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
        });

        const themeToggle = document.getElementById('themeToggle');
        const themeToggleIcon = document.getElementById('themeToggleIcon');
        const themeToggleText = document.getElementById('themeToggleText');

        const applyTheme = (isDark) => {
            document.body.classList.toggle('dark-mode', isDark);
            if (themeToggle) {
                const label = isDark ? 'Mode Terang' : 'Mode Gelap';
                const iconClass = isDark ? 'bi bi-toggle-on text-primary' : 'bi bi-toggle-off text-secondary';
                themeToggle.setAttribute('aria-label', label);
                themeToggle.title = label;
                if (themeToggleIcon) {
                    themeToggleIcon.className = iconClass;
                }
                if (themeToggleText) {
                    themeToggleText.textContent = label;
                }
            }
            localStorage.setItem('inventory-theme', isDark ? 'dark' : 'light');
        };

        const savedTheme = localStorage.getItem('inventory-theme');
        if (savedTheme === 'dark') {
            applyTheme(true);
        } else {
            applyTheme(false);
        }

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                applyTheme(!document.body.classList.contains('dark-mode'));
            });
        }

        const settingsDropdownBtn = document.getElementById('settingsDropdown');
        const settingsBackdrop = document.getElementById('settingsBackdrop');

        if (settingsDropdownBtn && settingsBackdrop) {
            const dropdownEl = settingsDropdownBtn.closest('.dropdown');

            if (dropdownEl) {
                dropdownEl.addEventListener('show.bs.dropdown', function () {
                    settingsBackdrop.classList.add('show');
                    document.body.classList.add('settings-open');
                });

                dropdownEl.addEventListener('hide.bs.dropdown', function () {
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
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
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

        const closeSidebar = () => {
            if (!appShell) return;
            if (isMobileScreen()) {
                appShell.classList.remove('sidebar-open');
            } else {
                appShell.classList.add('sidebar-collapsed');
                localStorage.setItem('inventory-sidebar-collapsed', 'true');
            }
        };

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', closeSidebar);
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
