<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function pdf()
    {
        $activeGrade = \App\Models\Setting::get('active_grade');
        
        $query = Candidate::query();
        
        if ($activeGrade) {
            $query->where('grado', $activeGrade);
        }
        
        $results = $query->orderBy('merit_order')->get();

        $tituloGrado = $activeGrade ? "EVALUACIÓN DE ASCENSO - GRADO: " . mb_strtoupper($activeGrade) : 'RESULTADO DEL PROCESO DE EVALUACIÓN';

        $pdf = Pdf::loadView('admin.reports.pdf', compact('results', 'tituloGrado'))
            ->setPaper('letter', 'portrait'); // Tamaño carta vertical

        return $pdf->download('reporte-resultados-votacion.pdf');
    }
}