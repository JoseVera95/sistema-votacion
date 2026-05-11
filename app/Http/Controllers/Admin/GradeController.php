<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Candidate::select('grado')->whereNotNull('grado')->where('grado', '!=', '')->distinct()->pluck('grado');
        $activeGrade = Setting::get('active_grade');
        $firmasJson = Setting::get('pdf_firmas', '[]');
        $firmas = json_decode($firmasJson, true) ?: [];

        return view('admin.grades.index', compact('grades', 'activeGrade', 'firmas'));
    }

    public function updateActive(Request $request)
    {
        $grade = $request->input('grade');
        // grade could be empty (meaning none)
        Setting::set('active_grade', $grade);

        return back()->with('success', 'Grado activo actualizado correctamente.');
    }

    public function resetVotes()
    {
        DB::transaction(function () {
            Vote::truncate();

            Voter::query()->update([
                'has_voted' => false,
                'voted_at' => null,
            ]);

            Candidate::query()->update([
                'first_place_votes' => 0,
                'points_total' => 0,
            ]);
            
            // Recalculate merit to restore initial order or alphabetical order
            $ordered = Candidate::orderBy('id')->get();
            foreach ($ordered as $index => $candidate) {
                $candidate->update([
                    'merit_order' => $index + 1,
                ]);
            }
        });

        return back()->with('success', 'Los votos han sido reiniciados correctamente.');
    }

    public function updateSignatures(Request $request)
    {
        $firmas = $request->input('firmas', []);
        // Remove empty strings
        $firmas = array_values(array_filter($firmas, function($f) {
            return trim($f) !== '';
        }));
        Setting::set('pdf_firmas', json_encode($firmas));

        return back()->with('success', 'Firmas del PDF actualizadas correctamente.');
    }
}
