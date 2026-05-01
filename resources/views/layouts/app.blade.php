<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') — SENSTOCK ITSM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --brand:        #889ABF;
            --brand-dark:   #6878A0;
            --brand-light:  #B8C4D8;
            --brand-pale:   #EEF1F7;
            --white:        #FFFFFF;
            --gray-50:      #F8F9FC;
            --gray-100:     #F0F2F8;
            --gray-200:     #DDE1EC;
            --gray-400:     #9BA3B8;
            --gray-600:     #5A6380;
            --gray-800:     #2D3348;
            --gray-900:     #1A1F2E;
            --danger:       #E85555;
            --warning:      #F5A623;
            --success:      #3DBB8A;
            --info:         #4A90D9;
            --sidebar-w:    260px;
            --header-h:     64px;
            --radius:       12px;
            --radius-sm:    8px;
            --shadow-sm:    0 1px 4px rgba(136,154,191,.12);
            --shadow:       0 4px 16px rgba(136,154,191,.18);
            --shadow-lg:    0 8px 32px rgba(136,154,191,.22);
            --transition:   all .2s ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--gray-900);
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 24px rgba(0,0,0,.15);
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            object-fit: contain;
            background: var(--white);
            padding: 4px;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand-name {
            color: var(--white);
            font-weight: 700;
            font-size: 15px;
            letter-spacing: .5px;
        }

        .sidebar-brand-sub {
            color: var(--brand-light);
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 1px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-section-label {
            color: var(--gray-400);
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 16px 8px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 2px;
            transition: var(--transition);
            position: relative;
        }

        .nav-item:hover {
            background: rgba(136,154,191,.15);
            color: var(--white);
        }

        .nav-item.active {
            background: var(--brand);
            color: var(--white);
            font-weight: 500;
        }

        .nav-item .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
            min-width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: var(--radius-sm);
            background: rgba(255,255,255,.05);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--brand);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { color: white; font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { color: var(--brand-light); font-size: 11px; margin-top: 1px; }

        .logout-btn {
            color: rgba(255,255,255,.5);
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: var(--transition);
        }
        .logout-btn:hover { color: var(--danger); background: rgba(232,85,85,.1); }

        /* ── Main ── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .topbar {
            height: var(--header-h);
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
        }

        .topbar-title {
            font-size: 17px;
            font-weight: 600;
            color: var(--gray-800);
            flex: 1;
        }

        .topbar-subtitle {
            font-size: 12px;
            color: var(--gray-400);
            font-weight: 400;
            margin-top: 1px;
        }

        .topbar-actions { display: flex; align-items: center; gap: 10px; }

        .topbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background: var(--brand);
            color: white;
        }
        .btn-primary:hover { background: var(--brand-dark); color: white; transform: translateY(-1px); box-shadow: var(--shadow); }

        .btn-outline {
            background: transparent;
            color: var(--gray-600);
            border: 1px solid var(--gray-200);
        }
        .btn-outline:hover { background: var(--gray-100); color: var(--gray-800); }

        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #c94444; color: white; }

        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #32a876; color: white; }

        .btn-warning { background: var(--warning); color: white; }
        .btn-warning:hover { background: #d4901e; color: white; }

        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ── Content ── */
        .content {
            flex: 1;
            padding: 28px;
        }

        /* ── Cards ── */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            padding: 18px 22px 16px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .card-body { padding: 22px; }

        /* ── Stats ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .stat-card:hover { box-shadow: var(--shadow); transform: translateY(-2px); }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-icon.brand   { background: var(--brand-pale); color: var(--brand-dark); }
        .stat-icon.success { background: #EBF9F4; color: var(--success); }
        .stat-icon.warning { background: #FEF5E7; color: var(--warning); }
        .stat-icon.danger  { background: #FDEEEE; color: var(--danger); }
        .stat-icon.info    { background: #EBF4FD; color: var(--info); }

        .stat-value { font-size: 28px; font-weight: 700; color: var(--gray-800); line-height: 1; }
        .stat-label { font-size: 12px; color: var(--gray-400); margin-top: 4px; font-weight: 500; }

        /* ── Table ── */
        .table-wrapper { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            background: var(--gray-50);
            padding: 11px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: var(--gray-400);
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--gray-200);
            white-space: nowrap;
        }

        tbody tr { border-bottom: 1px solid var(--gray-100); transition: var(--transition); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--brand-pale); }

        td { padding: 13px 14px; font-size: 13px; color: var(--gray-700); vertical-align: middle; }

        /* ── Badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-critique  { background: #FDEEEE; color: var(--danger); }
        .badge-haute     { background: #FEF5E7; color: var(--warning); }
        .badge-normale   { background: #EBF4FD; color: var(--info); }
        .badge-faible    { background: var(--gray-100); color: var(--gray-600); }
        .badge-ouvert    { background: var(--brand-pale); color: var(--brand-dark); }
        .badge-en_cours  { background: #FEF5E7; color: var(--warning); }
        .badge-en_attente { background: var(--gray-100); color: var(--gray-600); }
        .badge-resolu    { background: #EBF9F4; color: var(--success); }
        .badge-ferme     { background: var(--gray-100); color: var(--gray-800); }
        .badge-annule    { background: #FDEEEE; color: var(--danger); }
        .badge-panne     { background: #FDEEEE; color: var(--danger); }
        .badge-incident  { background: #FEF5E7; color: var(--warning); }
        .badge-demande   { background: var(--brand-pale); color: var(--brand-dark); }
        .badge-changement { background: #EBF4FD; color: var(--info); }

        /* ── Alerts ── */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-success { background: #EBF9F4; color: #2a7a5c; border-left: 4px solid var(--success); }
        .alert-danger  { background: #FDEEEE; color: #9b2c2c; border-left: 4px solid var(--danger); }
        .alert-warning { background: #FEF5E7; color: #8a5a0e; border-left: 4px solid var(--warning); }
        .alert-info    { background: #EBF4FD; color: #1e4e8c; border-left: 4px solid var(--info); }

        /* ── Forms ── */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-600);
            margin-bottom: 7px;
        }

        .form-label .required { color: var(--danger); margin-left: 2px; }

        .form-control, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            color: var(--gray-800);
            background: var(--white);
            transition: var(--transition);
            outline: none;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(136,154,191,.2);
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--danger);
        }

        .invalid-feedback {
            color: var(--danger);
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        textarea.form-control { resize: vertical; min-height: 100px; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 4px; justify-content: center; margin-top: 20px; }
        .pagination a, .pagination span {
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--gray-600);
            border: 1px solid var(--gray-200);
            text-decoration: none;
            transition: var(--transition);
        }
        .pagination a:hover { background: var(--brand-pale); border-color: var(--brand); color: var(--brand-dark); }
        .pagination .active span { background: var(--brand); color: white; border-color: var(--brand); }

        /* ── Ticket row link ── */
        .ticket-link { text-decoration: none; color: inherit; }
        .ticket-number { font-family: 'DM Mono', monospace; font-size: 12px; color: var(--brand); font-weight: 500; }

        /* ── SLA indicator ── */
        .sla-ok      { color: var(--success); }
        .sla-warning { color: var(--warning); }
        .sla-breach  { color: var(--danger); font-weight: 600; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
        }

        /* ── Scroll ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-200); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand-light); }
    </style>

    @stack('styles')
</head>
<body>

<!-- ── Sidebar ── -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-inner">
            <img src="{{ asset('images/logo.jpg') }}" alt="SENSTOCK" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">SENSTOCK</span>
                <span class="sidebar-brand-sub">ITSM Platform</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section-label">Principal</span>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high nav-icon"></i>
            Tableau de bord
        </a>

        <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-ticket nav-icon"></i>
            Tickets
            @php
                $openCount = \App\Models\Ticket::where('status','ouvert')->count();
            @endphp
            @if($openCount > 0)
                <span class="nav-badge">{{ $openCount }}</span>
            @endif
        </a>

        @if(auth()->user()->isITStaff())
        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-bar nav-icon"></i>
            Rapports & KPI
        </a>
        @endif

        @if(auth()->user()->isAdministrateur() || auth()->user()->isResponsableIT())
        <span class="nav-section-label">Administration</span>

        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="fa-solid fa-users nav-icon"></i>
            Utilisateurs
        </a>

        <a href="{{ route('admin.audit') }}" class="nav-item {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved nav-icon"></i>
            Audit & Sécurité
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role_label }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Déconnexion">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- ── Main ── -->
<div class="main-wrapper">
    <header class="topbar">
        <div style="flex:1">
            <div class="topbar-title">@yield('page-title', 'Tableau de bord')</div>
            @hasSection('page-subtitle')
            <div class="topbar-subtitle">@yield('page-subtitle')</div>
            @endif
        </div>
        <div class="topbar-actions">
            @yield('topbar-actions')
            <a href="{{ route('tickets.create') }}" class="topbar-btn btn-primary">
                <i class="fa-solid fa-plus"></i>
                Nouveau ticket
            </a>
        </div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
