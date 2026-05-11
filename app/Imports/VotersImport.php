<?php

namespace App\Imports;

use App\Models\Voter;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class VotersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Limpiar cédula
        $cedula = preg_replace('/[^0-9]/', '', $row['cedula']);

        // Buscar foto
        $extensiones = ['jpg', 'jpeg', 'png'];
        $rutaFoto = null;

        foreach ($extensiones as $ext) {
            $path = "voters/{$cedula}.{$ext}";

            if (Storage::disk('public')->exists($path)) {
                $rutaFoto = $path;
                break;
            }
        }

        return Voter::updateOrCreate(
            ['cedula' => $cedula],
            [
                'grado' => trim($row['grado']),
                'nombres' => trim($row['nombres']),
                'apellidos' => trim($row['apellidos']),
                'foto' => $rutaFoto, 
            ]
        );
    }

    public function rules(): array
    {
        return [
            '*.cedula' => ['required'],
            '*.grado' => ['required'],
            '*.nombres' => ['required'],
            '*.apellidos' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.cedula.required' => 'La cédula es obligatoria.',
            '*.grado.required' => 'El grado es obligatorio.',
            '*.nombres.required' => 'El nombre es obligatorio.',
            '*.apellidos.required' => 'El apellido es obligatorio.',
        ];
    }
}
