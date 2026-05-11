<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proceso de Votación</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: #f4f6f9;
        }

        .header-bar {
            background: #0d47a1;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .voter-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .voter-photo {
            width: 140px;
            height: 180px;
            object-fit: cover;
            border-radius: 15px;
            border: 4px solid #0d47a1;
            margin-top: -100px;
            background: white;
        }

        .candidate-card {
            transition: 0.25s;
            border-radius: 20px;
        }

        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .candidate-photo {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #0d47a1;
        }

        .footer {
            margin-top: 40px;
            padding: 15px;
            background: #0d47a1;
            color: white;
            text-align: center;
            font-size: 14px;
        }

        /* ===== DARK MODE ===== */
        body.dark-mode {
            background: #0f172a;
            color: #e2e8f0;
        }

        body.dark-mode .header-bar, body.dark-mode .footer {
            background: #1e293b;
            color: #cbd5e1;
        }

        body.dark-mode .footer {
            border-top: 1px solid #334155;
        }

        body.dark-mode .voter-card {
            background: #1e293b;
            color: #f8fafc;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        body.dark-mode .voter-photo {
            border-color: #3b82f6;
            background: #1e293b;
        }

        body.dark-mode .candidate-card {
            background: #1e293b;
            color: #f8fafc;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3) !important;
        }

        body.dark-mode .candidate-photo {
            border-color: #3b82f6;
        }

        body.dark-mode .form-select {
            background-color: #0f172a;
            color: #e2e8f0;
            border-color: #334155;
        }

        body.dark-mode .form-select:focus {
            border-color: #3b82f6;
        }

        body.dark-mode .modal-content {
            background: #1e293b;
            color: #e2e8f0;
        }

        body.dark-mode .modal-header {
            border-bottom-color: #334155;
        }

        body.dark-mode .modal-footer {
            border-top-color: #334155;
        }

        /* THEME TOGGLE FLOATING BUTTON */
        .theme-toggle-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 44px;
            height: 44px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: #1f2937;
            transition: all 0.2s ease;
        }

        .theme-toggle-float:hover {
            transform: translateY(-2px);
            background: #fff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        body.dark-mode .theme-toggle-float {
            background: rgba(30, 41, 59, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }

        body.dark-mode .theme-toggle-float:hover {
            background: #1e293b;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header-bar">
        SISTEMA DE VOTACIÓN
    </div>

    <!-- BOTÓN SALIR -->
    <div class="container mt-3 d-flex justify-content-end">
        <button class="btn btn-danger d-flex align-items-center gap-2 px-4 shadow"
            data-bs-toggle="modal" data-bs-target="#exitModal" style="border-radius: 25px;">
            <i class="bi bi-box-arrow-left"></i> Salir
        </button>
    </div>

    <div class="container py-5">

        <!-- VOTANTE -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-5">

                <div class="voter-card pt-5">

                    <img src="{{ $voter->foto ? asset('storage/' . $voter->foto) : asset('img/default-user.png') }}"
                        class="voter-photo shadow">

                    <h4 class="mt-3 fw-bold">
                        {{ $voter->nombres }} {{ $voter->apellido }}
                    </h4>

                    <p class="mb-1 text-muted">
                        Cédula: <strong>{{ $voter->cedula }}</strong>
                    </p>

                    <p class="mb-0">
                        Grado: <strong>{{ $voter->grado }}</strong>
                    </p>

                </div>
            </div>
        </div>

        <!-- FORM -->
        <form id="voteForm" action="{{ route('vote.store') }}" method="POST">
            @csrf

            <div class="row g-4">

                @forelse ($candidates as $candidate)
                    <div class="col-md-6 col-lg-4">
                        <div class="card candidate-card border-0 shadow-sm p-4 h-100">

                            <div class="text-center">

                                <img src="{{ $candidate->foto ? asset('storage/' . $candidate->foto) : asset('img/default-user.png') }}"
                                    class="candidate-photo mb-2">

                                <span class="badge bg-primary mb-2">
                                    Mérito N°{{ $candidate->merit_order }}
                                </span>

                                <div>
                                    <strong>{{ $candidate->grado }}</strong><br>
                                    <small>{{ $candidate->nombres_completos }}</small>
                                </div>

                            </div>

                            <div class="mt-3">
                                <label class="form-label fw-semibold">
                                    Orden de mérito
                                </label>

                                <select name="rank[{{ $candidate->id }}]" class="form-select rank-select">
                                    <option value="">Seleccione</option>

                                    @for ($i = 1; $i <= $candidates->count(); $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center shadow-sm">
                            <i class="bi bi-info-circle-fill me-2"></i> No hay candidatos disponibles para el grado activo en este momento.
                        </div>
                    </div>
                @endforelse

            </div>

            <!-- BOTÓN -->
            <button id="submitBtn" class="btn btn-primary btn-lg w-100 mt-4 shadow fw-bold" disabled>
                Confirmar voto
            </button>

        </form>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        Sistema de Votación de la Aviación Militar Bolivariana
    </div>

    <!-- MODAL SALIR -->
    <div class="modal fade" id="exitModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar salida</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <p class="fw-semibold text-danger mb-0">
                        ¿Está seguro que desea salir? Su voto no será guardado.
                    </p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        No
                    </button>

                    <a href="{{ route('home') }}" class="btn btn-danger px-4">
                        Sí, salir
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- FLOATING THEME TOGGLE -->
    <button id="themeToggle" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center theme-toggle-float" aria-label="Cambiar tema">
        <i class="bi bi-moon-stars"></i>
    </button>

    <!-- JS (NO TOCADO) -->
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

        let formChanged = false;
        let submittingVote = false;

        const submitBtn = document.getElementById('submitBtn');
        const voteForm = document.getElementById('voteForm');
        const selects = document.querySelectorAll('.rank-select');

        const totalOptions = {{ $candidates->count() }};

        function initializeSelects() {
            selects.forEach(select => {
                generateOptions(select, null);
            });
        }

        function generateOptions(select, selectedValue) {
            let usedValues = [];

            selects.forEach(s => {
                if (s !== select && s.value !== "") {
                    usedValues.push(s.value);
                }
            });

            select.innerHTML = '<option value="">Seleccione</option>';

            for (let i = 1; i <= totalOptions; i++) {
                if (!usedValues.includes(i.toString()) || i == selectedValue) {
                    let option = document.createElement('option');
                    option.value = i;
                    option.text = i;

                    if (selectedValue == i) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                }
            }
        }

        selects.forEach(select => {
            select.addEventListener('change', function() {
                formChanged = true;

                selects.forEach(s => {
                    generateOptions(s, s.value);
                });

                validateForm();
            });
        });

        function validateForm() {
            let anyFilled = false;
            let allFilled = true;

            selects.forEach(s => {
                if (s.value) {
                    anyFilled = true;
                } else {
                    allFilled = false;
                }
            });

            submitBtn.disabled = !anyFilled;
            window.allFilled = allFilled;
        }

        voteForm.addEventListener('submit', function(e) {
            if (!window.allFilled) {
                if (!confirm("Si realiza solo esta selección, el resto quedará en el mismo orden de mérito. ¿Desea continuar?")) {
                    e.preventDefault();
                    return;
                }
            }

            submittingVote = true;
            formChanged = false;

            submitBtn.disabled = true;
            submitBtn.innerText = "Procesando...";
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged && !submittingVote) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        initializeSelects();
        validateForm();
    </script>
</body>

</html>