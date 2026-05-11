@extends('admin.layout')

@section('title','Grados y Votaciones')

@section('content')

<div class="container py-4">

    <h3 class="mb-4">Gestión de Grados y Votaciones</h3>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- SELECCIONAR GRADO ACTIVO -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Grado Activo para Votación</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Seleccione qué grado se mostrará a los votantes. Si no selecciona ninguno, la lista aparecerá en blanco.</p>
                    <form action="{{ route('admin.grades.active') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Grado</label>
                            <select name="grade" class="form-select">
                                <option value="">-- Ninguno (Lista en blanco) --</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade }}" {{ $activeGrade === $grade ? 'selected' : '' }}>
                                        {{ $grade }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Guardar Grado Activo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- REINICIAR VOTOS -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Reiniciar Votaciones</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <p class="text-muted">Al hacer clic en este botón, se eliminarán <strong>todos</strong> los votos registrados, y el estado de los votantes se restablecerá.</p>
                    <form action="{{ route('admin.grades.reset') }}" method="POST" onsubmit="return confirm('¿Está absolutamente seguro de que desea reiniciar TODOS los votos? Esta acción no se puede deshacer.');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="bi bi-trash"></i> Reiniciar Todos los Votos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CONFIGURAR FIRMAS -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Firmas para el PDF</h5>
            <button type="button" class="btn btn-sm btn-light text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#addFirmaModal">
                <i class="bi bi-plus-lg"></i> Agregar Firma
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.grades.signatures') }}" method="POST">
                @csrf
                <p class="text-muted">Modifique o agregue los nombres de los firmantes que aparecerán en el reporte PDF. Los últimos aparecerán centrados automáticamente dependiendo de la cantidad.</p>
                <div class="row g-3" id="firmasContainer">
                    @foreach($firmas as $index => $firma)
                        <div class="col-md-3 firma-item">
                            <label class="form-label d-flex justify-content-between">
                                <span class="firma-label">Firma {{ $index + 1 }}</span>
                                <button type="button" class="btn btn-sm text-danger p-0 border-0 bg-transparent remove-firma" title="Eliminar" onclick="removeFirma(this)">
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </label>
                            <input type="text" name="firmas[]" class="form-control" value="{{ $firma }}" placeholder="Nombre del firmante">
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Firmas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar firma -->
<div class="modal fade" id="addFirmaModal" tabindex="-1" aria-labelledby="addFirmaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addFirmaModalLabel">Agregar Nueva Firma</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="newFirmaName" class="form-label">Nombre del firmante</label>
                    <input type="text" id="newFirmaName" class="form-control" placeholder="Ej. MG. JUAN PÉREZ">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="addFirma()">Agregar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function updateLabels() {
        const labels = document.querySelectorAll('.firma-item .firma-label');
        labels.forEach((label, index) => {
            label.textContent = 'Firma ' + (index + 1);
        });
    }

    function removeFirma(btn) {
        btn.closest('.firma-item').remove();
        updateLabels();
    }

    function addFirma() {
        const newNameInput = document.getElementById('newFirmaName');
        const newName = newNameInput.value.trim();
        
        if (newName === '') {
            alert('Por favor ingrese un nombre.');
            return;
        }

        const container = document.getElementById('firmasContainer');
        const itemCount = container.querySelectorAll('.firma-item').length + 1;
        
        const div = document.createElement('div');
        div.className = 'col-md-3 firma-item';
        div.innerHTML = `
            <label class="form-label d-flex justify-content-between">
                <span class="firma-label">Firma ${itemCount}</span>
                <button type="button" class="btn btn-sm text-danger p-0 border-0 bg-transparent remove-firma" title="Eliminar" onclick="removeFirma(this)">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </label>
            <input type="text" name="firmas[]" class="form-control" value="${newName}" placeholder="Nombre del firmante">
        `;
        
        container.appendChild(div);
        
        // Clean and close modal
        newNameInput.value = '';
        const modalEl = document.getElementById('addFirmaModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
        
        updateLabels();
    }
</script>

@endsection
