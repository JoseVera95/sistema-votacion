<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">

@php
    $siteName = \App\Models\Setting::get('site_name', 'Sistema de Votación');
    $logo     = \App\Models\Setting::get('logo');
@endphp
<link rel="icon" href="{{ $logo && trim($logo) !== '' ? asset('storage/' . $logo) : asset('img/escudo-institucion.png') }}">
<title>Login Admin - {{ $siteName }}</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg,#1e293b,#0f172a);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.card {
    width:400px;
    border-radius:20px;
}
</style>

</head>

<body>

<div class="card shadow-lg p-4">

<h4 class="text-center mb-3">Acceso Administrador</h4>

@if($errors->any())
<div class="alert alert-danger">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('admin.login.post') }}">
@csrf

<input type="text" name="username" class="form-control mb-3" placeholder="Usuario">

<input type="password" name="password" class="form-control mb-3" placeholder="Contraseña">

<button class="btn btn-dark w-100">Entrar</button>

</form>

<form method="POST" action="{{ route('home') }}">
    @csrf
    <button class="btn btn-link w-100 mt-3">Volver</button>
</form>

</div>

</body>
</html>