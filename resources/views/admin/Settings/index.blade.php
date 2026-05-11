@extends('admin.layout')

@section('title','Configuración del sistema')

@section('content')

<div class="container py-4">

    <h3 class="mb-4"> Configuración del Sistema</h3>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @php
            $banner = $settings['banner'] ?? null;
            $logo = $settings['logo'] ?? null;

            $bannerSrc = $banner && trim($banner) !== ''
                ? (str_starts_with($banner, 'data:image') ? $banner : asset('storage/'.$banner))
                : asset('img/banner-votacion.jpg');

            $logoSrc = $logo && trim($logo) !== ''
                ? (str_starts_with($logo, 'data:image') ? $logo : asset('storage/'.$logo))
                : asset('img/escudo-institucion.png');
        @endphp

        <div class="row g-4">

            <!-- NOMBRE -->
            <div class="col-md-6">
                <label class="form-label">Nombre del sistema</label>
                <input type="text" name="site_name" class="form-control"
                       value="{{ $settings['site_name'] ?? '' }}">
            </div>

            <!-- COLOR PRIMARIO -->
            <div class="col-md-3">
                <label class="form-label">Color primario</label>
                <input type="color" name="primary_color" class="form-control form-control-color"
                       value="{{ $settings['primary_color'] ?? '#0d47a1' }}">
            </div>

            <!-- COLOR SECUNDARIO -->
            <div class="col-md-3">
                <label class="form-label">Color secundario</label>
                <input type="color" name="secondary_color" class="form-control form-control-color"
                       value="{{ $settings['secondary_color'] ?? '#1565c0' }}">
            </div>

            <!-- BANNER -->
            <div class="col-md-6">
                <label class="form-label">Banner</label>

                <div class="mb-2">
                    <img id="previewBanner" src="{{ $bannerSrc }}"
                         style="width:100%; height:150px; object-fit:cover; border-radius:10px;">
                </div>

                <input type="file" name="banner" class="form-control" accept="image/*"
                       onchange="previewImage(event, 'previewBanner')">
            </div>

            <!-- LOGO -->
            <div class="col-md-6">
                <label class="form-label">Logo / Escudo</label>

                <div class="mb-2 text-center">
                    <img id="previewLogo" src="{{ $logoSrc }}"
                         style="width:100px; height:100px; object-fit:contain; border-radius:50%; border:1px solid #ddd;">
                </div>

                <input type="file" name="logo" class="form-control" accept="image/*"
                       onchange="previewImage(event, 'previewLogo')">
            </div>

            <!-- PDF TITLE -->
            <div class="col-md-6">
                <label class="form-label">Título PDF</label>
                <input type="text" name="pdf_title" class="form-control"
                       value="{{ $settings['pdf_title'] ?? '' }}">
            </div>

            <!-- PDF FOOTER -->
            <div class="col-md-6">
                <label class="form-label">Pie de página PDF</label>
                <input type="text" name="pdf_footer" class="form-control"
                       value="{{ $settings['pdf_footer'] ?? '' }}">
            </div>

        </div>

        <hr class="my-4">

        <button class="btn btn-primary px-4">
            💾 Guardar configuración
        </button>

    </form>

</div>

{{-- SCRIPT PREVIEW --}}
<script>
function previewImage(event, id) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById(id).src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

@endsection