@extends('admin.layout')

@section('title','Dashboard')

@section('content')

<h2 class="mb-4">Panel General</h2>

<div class="row g-3">

<div class="col-md-3">
<div class="card p-3">
<h6>Total votantes</h6>
<h2>{{ $totalVoters }}</h2>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6>Ya votaron</h6>
<h2 class="text-success">{{ $votedVoters }}</h2>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6>Pendientes</h6>
<h2 class="text-warning">{{ $pendingVoters }}</h2>
</div>
</div>

<div class="col-md-3">
<div class="card p-3">
<h6>Candidatos</h6>
<h2 class="text-primary">{{ $totalCandidates }}</h2>
</div>
</div>

</div>

<hr>

@if($topCandidate)
<div class="card p-4">
<h4>Candidato líder</h4>
<strong>{{ $topCandidate->nombres_completos }}</strong><br>
Grado: {{ $topCandidate->grado }}<br>
Puntos: {{ $topCandidate->points_total }}
</div>
@endif

@endsection