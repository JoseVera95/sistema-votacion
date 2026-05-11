<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Voto Registrado</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d47a1, #0b3c91);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-success {
            background: white;
            color: #333;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.6s ease;
        }

        .check-icon {
            font-size: 70px;
            color: #198754;
        }

        .title {
            color: #0d47a1;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn-exit {
            margin-top: 20px;
            padding: 12px;
            font-weight: bold;
            border-radius: 10px;
        }

        .footer {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            font-size: 14px;
            opacity: 0.9;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>

    <div class="card-success">

        <!-- ICONO -->
        <div class="check-icon">✔</div>

        <!-- TITULO -->
        <h3 class="title">VOTO REGISTRADO EXITOSAMENTE</h3>

        <!-- MENSAJE -->
        <p class="mt-3">
            Su participación ha sido registrada en el sistema.
            <br>
            Este proceso es <strong>único e intransferible</strong>.
        </p>

        <hr>

        <p><strong>Votante:</strong><br>
            {{ $voter->grado }} {{ $voter->nombres }} {{ $voter->apellidos }}
        </p>

        <p><strong>Cédula:</strong><br>
            {{ $voter->cedula }}
        </p>

        <div class="alert alert-success mt-3">
            ⚠️ No podrá votar nuevamente
        </div>

        <!-- BOTÓN SALIR -->
        <a href="{{ route('home') }}" class="btn btn-primary btn-exit w-100">
            Finalizar
        </a>

    </div>

    <div class="footer">
        Sistema de Votación de la Aviación Militar Bolivariana
    </div>

</body>

</html>
