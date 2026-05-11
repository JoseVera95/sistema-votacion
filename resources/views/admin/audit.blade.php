@extends('admin.layout')

@section('content')

<h3 class="fw-bold mb-4">Bitácora del Sistema</h3>

<div class="card shadow">
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Acción</th>
                    <th>Módulo</th>
                    <th>Descripción</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->model }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->user }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection