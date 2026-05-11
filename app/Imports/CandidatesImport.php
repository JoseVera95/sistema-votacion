<?php

namespace App\Imports;

use App\Models\Candidate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CandidatesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Limpiar cédula
        $cedula = preg_replace('/[^0-9]/', '', $row['cedula']);

        // Posibles extensiones
        $extensiones = ['jpg', 'jpeg', 'png'];
        $rutaFoto = null;

        foreach ($extensiones as $ext) {
            $path = "candidates/{$cedula}.{$ext}";

            if (Storage::disk('public')->exists($path)) {
                $rutaFoto = $path;
                break;
            }
        }

        return Candidate::updateOrCreate(
            ['cedula' => $cedula],
            [
                'grado' => trim($row['grado']),
                'nombres_completos' => trim($row['nombres_completos']),
                'merit_order' => (int) $row['merit_order'],
                'foto' => $rutaFoto, // 🔥 aquí se guarda automáticamente
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.cedula' => ['required'],
            '*.grado' => ['required'],
            '*.nombres_completos' => ['required'],
            '*.merit_order' => ['required', 'integer'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.cedula.required' => 'La cédula es obligatoria.',
            '*.grado.required' => 'El grado es obligatorio.',
            '*.nombres_completos.required' => 'El nombre completo es obligatorio.',
            '*.merit_order.required' => 'El orden de mérito es obligatorio.',
        ];
    }
}