<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Http\Controllers\Controller;
use App\Imports\CandidatesImport;
use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = Candidate::orderBy('merit_order')->get();
        return view('admin.candidates.index', compact('candidates'));
    }

    public function create()
    {
        return view('admin.candidates.create');
    }

    public function store(Request $request)
    {
            
        $data = $request->validate([
            'cedula' => ['required', 'string', Rule::unique('candidates', 'cedula')],
            'grado' => ['required', 'string', 'max:100'],
            'nombres_completos' => ['required', 'string', 'max:255'],
            'merit_order' => ['required', 'integer', 'min:1'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('candidates', 'public');
        }

        $candidate = Candidate::create($data);

        AuditHelper::log('create','Candidate',$candidate->id,"Candidato {$candidate->cedula} creado");

        return back()->with('success', 'Candidato creado correctamente.');
    }

    public function update(Request $request, Candidate $candidato)
    {
        
        $data = $request->validate([
            'cedula' => ['required', 'string', Rule::unique('candidates', 'cedula')->ignore($candidato->id)],
            'grado' => ['required', 'string', 'max:100'],
            'nombres_completos' => ['required', 'string', 'max:255'],
            'merit_order' => ['required', 'integer', 'min:1'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($request->hasFile('foto')) {
            if ($candidato->foto) {
                Storage::disk('public')->delete($candidato->foto);
            }
            $data['foto'] = $request->file('foto')->store('candidates', 'public');
        }

        $candidato->update($data);

        AuditHelper::log('update','Candidate',$candidato->id,"Candidato {$candidato->cedula} actualizado");

        return back()->with('success', 'Candidato actualizado correctamente.');
    }

    public function destroy(Candidate $candidato)
    {
        if (Vote::where('candidate_id', $candidato->id)->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene votos.');
        }

        if ($candidato->foto) {
            Storage::disk('public')->delete($candidato->foto);
        }

        $candidato->delete();

        AuditHelper::log('delete','Candidate',$candidato->id,"Candidato eliminado");

        return back()->with('success', 'Candidato eliminado.');
    }

    public function importExcel(Request $request)
    {
        
        try {
            $request->validate([
                'file' => ['required','file','mimes:xlsx,xls,csv']
            ]);

            Excel::import(new CandidatesImport, $request->file('file'));

            AuditHelper::log('import','Candidate',null,'Importación Excel candidatos');

            return back()->with('success','✔ Importación correcta');

        } catch (\Exception $e) {
            return back()->with('error','❌ '.$e->getMessage());
        }
    }

    public function uploadPhotos(Request $request)
    {
        try {

            $request->validate([
                'photos' => ['required','array'],
                'photos.*' => ['image','mimes:jpg,jpeg,png,webp','max:10240']
            ]);

            $extensions = ['jpg','jpeg','png','webp'];

            $assigned = 0;
            $notMatched = [];
            $uploadedNames = [];

            // GUARDAR
            foreach ($request->file('photos') as $photo) {

                $name = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $photo->extension();

                $photo->storeAs('candidate_photos', $name.'.'.$ext, 'public');

                $uploadedNames[] = $name;
            }

            // ASIGNAR
            foreach (Candidate::all() as $candidate) {

                $found = null;

                foreach ($extensions as $ext) {
                    $path = "candidate_photos/{$candidate->cedula}.{$ext}";

                    if (Storage::disk('public')->exists($path)) {
                        $found = $path;
                        break;
                    }
                }

                if ($found) {
                    $candidate->foto = $found;
                    $candidate->save();
                    $assigned++;
                }
            }

            // NO COINCIDEN
            foreach ($uploadedNames as $name) {
                if (!Candidate::where('cedula', $name)->exists()) {
                    $notMatched[] = $name;
                }
            }

            $withoutPhoto = Candidate::whereNull('foto')->count();

            $msg = "✔ Asignadas: {$assigned}<br>";

            if ($notMatched) {
                $msg .= "⚠️ Sin coincidencia: ".implode(', ', $notMatched)."<br>";
            }

            if ($withoutPhoto) {
                $msg .= "⚠️ Sin foto: {$withoutPhoto}";
            }

            AuditHelper::log('upload','Candidate',null,'Carga masiva de fotos');

            return back()->with('success',$msg);

        } catch (\Exception $e) {
            return back()->with('error','❌ '.$e->getMessage());
        }
    }
}