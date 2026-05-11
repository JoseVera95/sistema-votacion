<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">

<style>
    /* DomPDF se lleva mejor con estilos inline o en el head, usando DejaVu Sans para acentos */
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        margin: 30px 40px;
        color: #000;
    }
    
    /* Estilo para las firmas */
    .firma-text {
        font-size: 8px;
        font-weight: bold;
        text-align: center;
        line-height: 1.3;
    }
    .linea-firma {
        border-top: 1px solid #000;
        margin-bottom: 4px;
        margin-top: 50px; /* Espacio para que quepa la firma manuscrita */
    }
</style>

</head>
<body>

@php
    // Obtener logo dinámico o usar uno por defecto. Verificamos si existe para evitar que DOMPDF falle.
    $logo = \App\Models\Setting::get('logo');
    $logoPath = $logo ? public_path('storage/' . $logo) : public_path('img/escudo_izq.png');
    $logoExists = file_exists($logoPath);
@endphp

{{-- =============================================
     ENCABEZADO CON 2 ESCUDOS (Formato Original)
     ============================================= --}}
<table width="100%" border="0" align="center" style="margin-bottom: 5px;">
    <tr>
        {{-- ESCUDO IZQUIERDO --}}
        <td width="15%" align="right" valign="top">
            @if($logoExists)
                <img src="{{ $logoPath }}" width="50" height="50">
            @endif
        </td>

        {{-- TEXTO INSTITUCIONAL --}}
        <td width="70%" align="center" valign="middle">
            <strong style="font-size: 10px;">REPÚBLICA BOLIVARIANA DE VENEZUELA</strong><br>
            <strong style="font-size: 10px;">MINISTERIO DEL PODER POPULAR PARA LA DEFENSA</strong><br>
            <strong style="font-size: 10px;">AVIACIÓN MILITAR BOLIVARIANA</strong><br>
            <strong style="font-size: 10px;">JUNTA PERMANENTE DE EVALUACIÓN</strong>
        </td>

        {{-- ESCUDO DERECHO --}}
        <td width="15%" align="left" valign="top">
            @if($logoExists)
                <img src="{{ $logoPath }}" width="50" height="50">
            @endif
        </td>
    </tr>
</table>

{{-- =============================================
     FECHA Y LUGAR
     ============================================= --}}
<table width="100%" border="0">
    <tr>
        <td align="right" style="font-size: 11px;">
            Fuerte Tiuna, {{ date('d/m/Y') }}
        </td>
    </tr>
</table>

{{-- =============================================
     TÍTULOS DEL REPORTE
     ============================================= --}}
<div align="center" style="margin-top: 15px; margin-bottom: 5px;">
    <strong style="font-size: 13px; color: #0000FF;">
        RESULTADO FINAL DEL PROCESO DE EVALUACIÓN DE ASCENSO AÑO {{ date('Y') }}
    </strong>
</div>

<div align="center" style="margin-bottom: 5px;">
    <strong style="font-size: 11px; color: #0000FF;">
        JUNTA CALIFICADORA
    </strong>
</div>

<div align="center" style="margin-bottom: 20px;">
    <strong style="font-size: 11px; color: #0000FF;">
        {{ $tituloGrado }}
    </strong>
</div>

{{-- =============================================
     TABLA DE RESULTADOS
     ============================================= --}}
<table width="100%" border="1" align="center" style="border-collapse: collapse; margin-bottom: 30px;">
    <thead>
        <tr style="background-color: #d9d9d9;">
            <th style="width: 10%; text-align: center; font-size: 10px;">ORDEN</th>
            <th style="width: 15%; text-align: center; font-size: 10px;">CEDULA</th>
            <th style="width: 20%; text-align: center; font-size: 10px;">GRADO</th>
            <th style="width: 55%; text-align: center; font-size: 10px;">NOMBRES Y APELLIDOS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $index => $c)
        <tr>
            <td style="text-align: center; font-size: 10px;">{{ $index + 1 }}</td>
            <td style="text-align: center; font-size: 10px;">{{ $c->cedula }}</td>
            <td style="text-align: center; font-size: 10px;">{{ $c->grado }}</td>
            <td style="text-align: left; padding-left: 8px; font-size: 10px;">{{ $c->nombres_completos }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@php
    $firmasJson = \App\Models\Setting::get('pdf_firmas', '[]');
    $firmas = json_decode($firmasJson, true) ?: [];
    $firmas = array_filter($firmas, function($f) { return trim($f) !== ''; });
    $chunks = array_chunk($firmas, 4);
@endphp

{{-- =============================================
     FIRMAS (Generadas Dinámicamente)
     ============================================= --}}
<div style="margin-top: 10px;">
    @foreach($chunks as $chunk)
        @php
            $count = count($chunk);
            $tableWidth = $count * 25; // 25, 50, 75 or 100
            $cellWidth = 100 / $count;
        @endphp
        <table width="{{ $tableWidth }}%" align="center" style="margin-bottom: 10px; margin-left: auto; margin-right: auto;">
            <tr>
                @foreach($chunk as $firma)
                    <td width="{{ $cellWidth }}%" style="text-align: center; padding: 0 10px;">
                        <div class="linea-firma"></div>
                        <div class="firma-text">{{ mb_strtoupper($firma) }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endforeach
</div>

{{-- =============================================
     PIE DE PÁGINA DINÁMICO
     ============================================= --}}
<div style="margin-top: 40px; text-align: center; border-top: 1px solid #ccc; padding-top: 10px;">
    <strong style="font-size: 9px;">{{ \App\Models\Setting::get('pdf_title') }}</strong>
    <div style="font-size: 8px; color: #555; margin-top: 3px;">
        {{ \App\Models\Setting::get('pdf_footer') }}
    </div>
</div>

</body>
</html>