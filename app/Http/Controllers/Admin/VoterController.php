<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Http\Controllers\Controller;
use App\Imports\VotersImport;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class VoterController extends Controller
{
    public function index()
    {
        $voters = Voter::withCount('votes')->latest()->get();
        return view('admin.voters.index', compact('voters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'grado' => ['required', 'string', 'max:100'],
            'cedula' => ['required', 'string', Rule::unique('voters', 'cedula')],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('voters', 'public');
        }

        $voter = Voter::create($data);

        AuditHelper::log(
            'create',
            'Voter',
            $voter->id,
            "Votante {$voter->cedula} creado correctamente"
        );

        return redirect()->route('admin.votantes.index')
            ->with('success', 'Votante creado correctamente.');
    }

    public function update(Request $request, Voter $votante)
    {
        
        $data = $request->validate([
            'grado' => ['required', 'string', 'max:100'],
            'cedula' => ['required', 'string', Rule::unique('voters', 'cedula')->ignore($votante->id)],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'has_voted' => ['nullable', 'boolean'],
        ]);

        $data['has_voted'] = $request->boolean('has_voted');

        if ($request->hasFile('foto')) {
            if ($votante->foto) {
                Storage::disk('public')->delete($votante->foto);
            }

            $data['foto'] = $request->file('foto')->store('voters', 'public');
        }

        $votante->update($data);

        AuditHelper::log(
            'update',
            'Voter',
            $votante->id,
            "Votante {$votante->cedula} actualizado correctamente"
        );

        return redirect()->route('admin.votantes.index')
            ->with('success', 'Votante actualizado correctamente.');
    }

    public function destroy(Voter $votante)
    {
        
        if ($votante->foto) {
            Storage::disk('public')->delete($votante->foto);
        }

        $votante->delete();

        AuditHelper::log(
            'delete',
            'Voter',
            $votante->id,
            "Votante {$votante->cedula} eliminado correctamente"
        );

        return back()->with('success', 'Votante eliminado correctamente.');
    }

    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
            ]);

            Excel::import(new VotersImport, $request->file('file'));

            AuditHelper::log(
                'import',
                'Voter',
                null,
                'Importación masiva de votantes vía Excel'
            );

            return back()->with('success', ' Votantes importados correctamente.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $errors = collect($e->failures())->map(function ($fail) {
                return "Fila {$fail->row()}: " . implode(', ', $fail->errors());
            });

            return back()->with('error', $errors->implode('<br>'));
        } catch (\Exception $e) {

            return back()->with('error', ' Error: ' . $e->getMessage());
        }
    }

    public function uploadPhotos(Request $request)
    {
        try {

            $request->validate([
                'photos' => ['required', 'array'],
                'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240']
            ]);

            $extensions = ['jpg', 'jpeg', 'png', 'webp'];

            $assigned = 0;
            $notMatched = [];
            $uploadedNames = [];


            foreach ($request->file('photos') as $photo) {

                $name = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $photo->extension();

                $photo->storeAs('voter_photos', $name . '.' . $extension, 'public');

                $uploadedNames[] = $name;
            }


            foreach (Voter::all() as $voter) {

                $found = null;

                foreach ($extensions as $ext) {

                    $path = "voter_photos/{$voter->cedula}.{$ext}";

                    if (Storage::disk('public')->exists($path)) {
                        $found = $path;
                        break;
                    }
                }

                if ($found) {
                    $voter->foto = $found;
                    $voter->save();
                    $assigned++;
                }
            }


            foreach ($uploadedNames as $name) {

                if (!Voter::where('cedula', $name)->exists()) {
                    $notMatched[] = $name;
                }
            }


            $withoutPhoto = Voter::whereNull('foto')->count();


            $message = "✔ Fotos asignadas: {$assigned}<br>";

            if (count($notMatched)) {
                $message .= " No coinciden: " . count($notMatched) . " (" . implode(', ', $notMatched) . ")<br>";
            }

            if ($withoutPhoto > 0) {
                $message .= " Votantes sin foto: {$withoutPhoto}";
            }

            AuditHelper::log(
                'upload',
                'Voter',
                null,
                "Carga masiva de fotos: {$assigned} asignadas, " . count($notMatched) . " sin coincidencia"
            );

            return back()->with('success', $message);
        } catch (\Exception $e) {

            return back()->with('error', 'Error al subir fotos: ' . $e->getMessage());
        }
    }
}
