<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $siteName = \App\Models\Setting::get('site_name', 'Sistema de Votación');
        $logo     = \App\Models\Setting::get('logo');
    @endphp
    <link rel="icon" href="{{ $logo && trim($logo) !== '' ? asset('storage/' . $logo) : asset('img/escudo-institucion.png') }}">
    <title>@yield('title', 'Panel Administrativo') - {{ $siteName }}</title>


    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body.dark-mode .form-control, body.dark-mode .form-select {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #334155;
        }

        body.dark-mode .form-control::placeholder {
            color: #94a3b8;
        }
        
        body.dark-mode .form-control:focus, body.dark-mode .form-select:focus {
            background: #0f172a;
            border-color: #3b82f6;
            color: #f8fafc;
        }

        body.dark-mode .table {
            color: #e2e8f0;
        }

        body.dark-mode .table thead th {
            background: #1e293b;
            color: #cbd5e1;
            border-bottom: 1px solid #334155;
        }

        body.dark-mode .table tbody td, body.dark-mode .table tbody tr {
            border-color: #334155;
            background: #0f172a;
            color: #e2e8f0;
        }

        body.dark-mode .card,
        body.dark-mode .topbar {
            background: var(--bg-card);
            border-color: #334155;
        }

        body.dark-mode .topbar {
            border-bottom: 1px solid #334155 !important;
        }

        body.dark-mode .text-muted {
            color: #94a3b8 !important;
        }

        body.dark-mode .modal-content {
            background: #1e293b;
            color: #e2e8f0;
        }
        
        body.dark-mode .modal-header,
        body.dark-mode .modal-footer {
            border-color: #334155;
        }
        
        body.dark-mode .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        :root {
            --bg-main: #f4f6f9;
            --bg-card: #ffffff;
            --text-main: #1f2937;
            --primary: #0d47a1;
        }

        body.dark-mode {
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --text-main: #e2e8f0;
            --primary: #3b82f6;
        }

        body {
            background: var(--bg-main);
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        .card {
            background: var(--bg-card);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
        }

        body {
            background: #f4f6f9;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: #0d47a1;
            color: white;
            transition: all 0.3s;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .logo {
            padding: 15px;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar.collapsed a span {
            display: none;
        }

        /* CONTENIDO */
        .content {
            margin-left: 250px;
            transition: 0.3s;
        }

        .content.collapsed {
            margin-left: 80px;
        }

        /* HEADER */
        .topbar {
            background: white;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
        }

        .card-soft {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .btn-soft {
            border-radius: 10px;
        }

        .btn-fixed {
            min-width: 160px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">

        <div class="logo">
            <i class="bi bi-shield-lock"></i>
            <span>ADMIN</span>
        </div>

        <a href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.candidatos.index') }}">
            <i class="bi bi-people"></i>
            <span>Candidatos</span>
        </a>

        <a href="{{ route('admin.votantes.index') }}">
            <i class="bi bi-person-badge"></i>
            <span>Votantes</span>
        </a>

        <a href="{{ route('admin.report.pdf') }}">
            <i class="bi bi-file-earmark-pdf"></i>
            <span>Reporte PDF</span>
        </a>

        <li class="nav-item mt-2">
            <a href="{{ route('admin.grades.index') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.grades.*') ? 'active fw-bold' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span>Lista de Grados</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.settings') }}"
                class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('admin.settings') ? 'active fw-bold' : '' }}">

                <i class="bi bi-gear-fill"></i>
                <span>Configuración</span>
            </a>
        </li>

    </div>

    <!-- CONTENIDO -->
    <div class="content" id="content">

        <!-- TOPBAR -->
        <div class="topbar d-flex justify-content-between align-items-center">

            <button class="btn btn-outline-primary btn-sm" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-flex align-items-center gap-3">
            
                <button id="themeToggle" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" aria-label="Cambiar tema">
                    <i class="bi bi-moon-stars"></i>
                </button>

                <span class="fw-semibold text-muted">Administrador</span>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger btn-sm btn-soft">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </button>
                </form>

            </div>

        </div>

        {{-- ALERTAS DEL SISTEMA --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                {!! session('success') !!}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                {!! session('error') !!}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning shadow-sm">
                <strong>⚠️ Errores detectados:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- CONTENIDO DINÁMICO -->
        <div class="container-fluid p-4">

            <div class="mb-3">
                <h4 class="fw-bold">@yield('title')</h4>
                <p class="text-muted">@yield('subtitle')</p>
            </div>

            @yield('content')

        </div>

        <!-- FOOTER -->
        <div class="footer">
            Sistema de Votación de la Aviación Militar Bolivariana
        </div>

    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const toggleBtn = document.getElementById('themeToggle');
        const themeIcon = toggleBtn.querySelector('i');

        function updateIcon() {
            if (document.body.classList.contains('dark-mode')) {
                themeIcon.className = 'bi bi-sun-fill';
            } else {
                themeIcon.className = 'bi bi-moon-stars';
            }
        }

        // Cargar preferencia guardada
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
        updateIcon();

        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            updateIcon();

            // Guardar preferencia
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        }
    </script>


</body>

</html>
