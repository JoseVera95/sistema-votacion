<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $siteName  = \App\Models\Setting::get('site_name', 'Sistema de Votación');
        $primary   = \App\Models\Setting::get('primary_color', '#0d47a1');
        $secondary = \App\Models\Setting::get('secondary_color', '#1565c0');
        $banner    = \App\Models\Setting::get('banner');
        $logo      = \App\Models\Setting::get('logo');
    @endphp

    <link rel="icon" href="{{ $logo && trim($logo) !== '' ? asset('storage/' . $logo) : asset('img/escudo-institucion.png') }}">
    <title>{{ $siteName }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <style>
        :root {
            --primary-blue: {{ $primary }};
            --primary-blue-dark: {{ $secondary }};
            --bg-soft: #f3f6fb;
            --text-dark: #1f2937;
        }
    </style>
</head>

<body>

    <div class="page-shell">

        {{-- PANEL IZQUIERDO: SOLO LA IMAGEN, SIN NADA ENCIMA --}}
        <div class="panel-left">
            @php
                $bannerSrc = $banner && trim($banner) !== ''
                    ? asset('storage/' . $banner)
                    : asset('img/banner-votacion.jpg');
            @endphp
            <img src="{{ $bannerSrc }}" class="banner-img" alt="Banner">
        </div>

        {{-- PANEL DERECHO: FORMULARIO --}}
        <div class="panel-right">

            <div class="form-area">

                @if (session('error'))
                    <div class="alert alert-danger shadow-sm w-100" style="max-width: 420px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success shadow-sm w-100" style="max-width: 420px;">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Logo --}}
                <div class="logo-wrap">
                    @if ($logo && trim($logo) !== '')
                        <img src="{{ asset('storage/' . $logo) }}" alt="Escudo institucional">
                    @else
                        <img src="{{ asset('img/escudo-institucion.png') }}" alt="Escudo institucional">
                    @endif
                </div>

                <h2 class="section-title">Ingreso de Votantes</h2>
                <p class="section-subtitle">
                    Ingrese su cédula para acceder al proceso de votación.
                </p>

                {{-- Formulario --}}
                <div class="auth-card">
                    <div class="card-body">

                        <form action="{{ route('voter.enter') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Ingrese su cédula</label>
                                <input type="text" name="cedula" class="form-control form-control-lg"
                                    placeholder="Cédula" required>
                                @error('cedula')
                                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary-custom btn-lg w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Votar
                            </button>
                        </form>

                        <div class="d-grid mt-3">
                            <a href="{{ route('admin.login') }}" class="btn btn-dark btn-admin btn-lg">
                                <i class="bi bi-person-lock me-1"></i> Administrador
                            </a>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <footer class="page-footer">
                <p>Copyright &copy; 2026 Aviación Militar Bolivariana. Todos los Derechos Reservados.</p>
                <p>Sistema Diseñado por la Dirección de Técnologia de la Información y las Comunicaciones de la Aviación Militar Bolivariana</p>
            </footer>

        </div>

    </div>

    <!-- FLOATING THEME TOGGLE -->
    <button id="themeToggle" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center theme-toggle-float" aria-label="Cambiar tema">
        <i class="bi bi-moon-stars"></i>
    </button>

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

        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
        updateIcon();

        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            updateIcon();

            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });
    </script>

</body>

</html>