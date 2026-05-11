<?php

namespace App\Console\Commands;

use App\Models\Voter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AssignVoterPhotos extends Command
{
    protected $signature = 'voters:assign-photos';
    protected $description = 'Asigna fotos a votantes buscando imágenes por cédula';

    public function handle()
    {
        $folder = 'voter_photos'; // storage/app/public/voter_photos
        $extensions = ['jpg', 'jpeg', 'png', 'webp'];

        $voters = Voter::all();
        $updated = 0;

        foreach ($voters as $voter) {
            $foundPath = null;

            foreach ($extensions as $ext) {
                $relative = $folder . '/' . $voter->cedula . '.' . $ext;

                if (Storage::disk('public')->exists($relative)) {
                    $foundPath = $relative;
                    break;
                }
            }

            if ($foundPath) {
                $voter->foto = $foundPath;
                $voter->save();
                $updated++;
            }
        }

        $this->info("Fotos asignadas correctamente a {$updated} votantes.");

        return Command::SUCCESS;
    }
}