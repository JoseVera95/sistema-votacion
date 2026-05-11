@extends('admin.layout')

@section('title', 'Candidatos')
@section('subtitle', 'Crear, editar y eliminar candidatos')

@section('content')

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row g-4">

            <div class="col-md-6">
                <form method="POST" action="{{ route('admin.candidatos.import') }}" enctype="multipart/form-data" class="h-100 d-flex flex-column justify-content-between">
                    @csrf

                    <div>
                        <label class="fw-bold mb-2">Importar Excel</label>
                        <input type="file" name="file" class="form-control mb-2" required>
                        <small class="text-muted">
                            Archivo .xlsx, .xls o .csv
                        </small>
                    </div>

                    <button class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-file-earmark-excel"></i> Importar
                    </button>
                </form>
            </div>

            <div class="col-md-6">
                <form method="POST" action="{{ route('admin.candidatos.upload_photos') }}" enctype="multipart/form-data" class="h-100 d-flex flex-column justify-content-between">
                    @csrf

                    <div>
                        <label class="fw-bold mb-2">Subir fotos</label>
                        <input type="file" name="photos[]" multiple class="form-control mb-2" required>
                        <small class="text-muted">
                            Nombre del archivo: <strong>cédula.jpg</strong>
                        </small>
                    </div>

                    <button class="btn btn-success w-100 mt-3">
                        <i class="bi bi-images"></i> Subir y asignar
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h2 class="fw-bold mb-1">Candidatos</h2>
        <p class="text-muted mb-0">Gestión institucional</p>
    </div>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createCandidateModal">
        + Nuevo candidato
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
                    <th>Nombre</th>
                    <th>Orden</th>
                    <th>1er Lugar</th>
                    <th>Puntos</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($candidates as $candidate)
                <tr>
                    <td style="width:90px;">
                        <img src="{{ $candidate->foto ? asset('storage/'.$candidate->foto) : asset('img/default-user.png') }}"
                             width="50" height="50"
                             class="rounded-circle border"
                             style="object-fit:cover;">
                    </td>

                    <td>{{ $candidate->cedula }}</td>
                    <td>{{ $candidate->grado }}</td>
                    <td>{{ $candidate->nombres_completos }}</td>

                    <td>
                        <span class="badge bg-primary">
                            {{ $candidate->merit_order }}
                        </span>
                    </td>

                    <td>{{ $candidate->first_place_votes }}</td>
                    <td>{{ $candidate->points_total }}</td>

                    <td class="text-end">
                        <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editCandidateModal{{ $candidate->id }}">
                            Editar
                        </button>

                        <form method="POST"
                              action="{{ route('admin.candidatos.destroy', $candidate) }}"
                              class="d-inline"
                              onsubmit="return confirm('¿Eliminar candidato?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center p-4 text-muted">
                        Sin registros
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal fade" id="createCandidateModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.candidatos.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Nuevo candidato</h5>
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

                        <div class="col-md-8">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="nombres_completos" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Orden</label>
                            <input type="number" name="merit_order" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control">
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

{{-- MODALES EDITAR --}}
@foreach($candidates as $candidate)
<div class="modal fade" id="editCandidateModal{{ $candidate->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('admin.candidatos.update', $candidate) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Editar candidato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Cédula</label>
                            <input type="text" name="cedula" class="form-control" value="{{ $candidate->cedula }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Grado</label>
                            <input type="text" name="grado" class="form-control" value="{{ $candidate->grado }}" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" name="nombres_completos" class="form-control" value="{{ $candidate->nombres_completos }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Orden</label>
                            <input type="number" name="merit_order" class="form-control" value="{{ $candidate->merit_order }}" min="1" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control">
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
@endforeach

@endsection