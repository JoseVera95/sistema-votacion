@extends('admin.layout')

@section('title', 'Votantes')
@section('subtitle', 'Crear, editar y eliminar votantes')

@section('content')

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row g-4">

            <div class="col-md-6">
                <form action="{{ route('admin.votantes.import') }}" method="POST" enctype="multipart/form-data" class="h-100 d-flex flex-column justify-content-between">
                    @csrf

                    <div>
                        <label class="fw-bold mb-2">Importar Excel</label>

                        <input type="file" name="file"
                            class="form-control mb-2 @error('file') is-invalid @enderror"
                            accept=".xlsx,.xls,.csv" required>

                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Columnas: cedula, grado, nombres, apellidos
                        </small>
                    </div>

                    <button class="btn btn-primary w-100 mt-3">
                        Importar
                    </button>
                </form>
            </div>

            <div class="col-md-6">
                <form action="{{ route('admin.votantes.upload_photos') }}" method="POST" enctype="multipart/form-data" class="h-100 d-flex flex-column justify-content-between">
                    @csrf

                    <div>
                        <label class="fw-bold mb-2">Subir fotos</label>

                        <input type="file" name="photos[]" class="form-control mb-2" multiple required>

                        <small class="text-muted">
                            Nombre del archivo: <strong>cédula.jpg</strong>
                        </small>
                    </div>

                    <button class="btn btn-success w-100 mt-3">
                        Subir fotos
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Votantes</h2>
        <p class="text-muted mb-0">Gestión institucional de votantes</p>
    </div>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createVoterModal">
        + Nuevo votante
    </button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Foto</th>
                    <th>Cédula</th>
                    <th>Grado</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($voters as $voter)
                <tr>
                    <td style="width:90px;">
                        <img src="{{ $voter->foto ? asset('storage/'.$voter->foto) : asset('img/default-user.png') }}"
                             width="50" height="50"
                             class="rounded-circle border"
                             style="object-fit:cover;">
                    </td>

                    <td>{{ $voter->cedula }}</td>
                    <td>{{ $voter->grado }}</td>
                    <td>{{ $voter->nombres }}</td>
                    <td>{{ $voter->apellidos }}</td>

                    <td>
                        @if($voter->has_voted)
                            <span class="badge bg-success">VOTÓ</span>
                        @else
                            <span class="badge bg-secondary">PENDIENTE</span>
                        @endif
                    </td>

                    <td>
                        {{ $voter->voted_at ? $voter->voted_at->format('d/m/Y H:i') : '-' }}
                    </td>

                    <td class="text-end">
                        <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editVoterModal{{ $voter->id }}">
                            Editar
                        </button>

                        <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteVoterModal{{ $voter->id }}">
                            Eliminar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted p-4">
                        Sin registros
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="createVoterModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('admin.votantes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Nuevo votante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Cédula</label>
                            <input type="text" name="cedula" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Grado</label>
                            <input type="text" name="grado" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombres</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Guardar</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- MODALES EDITAR Y ELIMINAR --}}
@foreach($voters as $voter)
<div class="modal fade" id="editVoterModal{{ $voter->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form action="{{ route('admin.votantes.update', $voter) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Editar votante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Cédula</label>
                            <input type="text" name="cedula" class="form-control" value="{{ $voter->cedula }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Grado</label>
                            <input type="text" name="grado" class="form-control" value="{{ $voter->grado }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cambiar foto</label>
                            <input type="file" name="foto" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombres</label>
                            <input type="text" name="nombres" class="form-control" value="{{ $voter->nombres }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" value="{{ $voter->apellidos }}" required>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_voted" value="1" id="hv{{ $voter->id }}" {{ $voter->has_voted ? 'checked' : '' }}>
                                <label class="form-check-label" for="hv{{ $voter->id }}">
                                    Marcar como votó
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-warning">Actualizar</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="deleteVoterModal{{ $voter->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="fw-bold mt-3 mb-1">¿Eliminar este votante?</p>
                <p class="text-muted small mb-0">
                    Esta acción es irreversible.
                </p>

                @if($voter->votes_count > 0)
                    <div class="alert alert-warning mt-3 mb-0">
                        ⚠️ Este votante ya participó en la votación.<br>
                        No podrá ser eliminado.
                    </div>
                @endif
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>

                @if($voter->votes_count == 0)
                <form action="{{ route('admin.votantes.destroy', $voter) }}" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger px-4 fw-bold">Sí, eliminar</button>
                </form>
                @else
                    <button class="btn btn-danger px-4" disabled>No permitido</button>
                @endif
            </div>

        </div>
    </div>
</div>
@endforeach

@endsection