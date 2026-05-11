@extends('admin.layout')

@section('title','Votantes')

@section('content')

<h2>Votantes</h2>

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrear">
Nuevo
</button>

<table class="table">
<tr>
<th>Grado</th>
<th>Cédula</th>
<th>Nombre</th>
<th>Votó</th>
<th>Acciones</th>
</tr>

@foreach($voters as $v)
<tr>
<td>{{ $v->grado }}</td>
<td>{{ $v->cedula }}</td>
<td>{{ $v->nombres }}</td>
<td>{{ $v->has_voted ? 'SI' : 'NO' }}</td>
<td>

<form method="POST" action="{{ route('admin.votantes.destroy',$v) }}">
@csrf @method('DELETE')
<button class="btn btn-danger btn-sm">Eliminar</button>
</form>

</td>
</tr>
@endforeach
</table>

<div class="modal fade" id="modalCrear">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="{{ route('admin.votantes.store') }}">
@csrf

<div class="modal-body">
<input name="cedula" class="form-control mb-2" placeholder="Cedula">
<input name="nombres" class="form-control mb-2" placeholder="Nombre">
<input name="apellidos" class="form-control" placeholder="Apellido">
</div>

<button class="btn btn-primary">Crear</button>

</form>
</div>
</div>
</div>

@endsection