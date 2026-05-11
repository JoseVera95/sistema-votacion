<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'grado',
        'cedula',
        'nombres_completos',
        'foto',
        'merit_order',
        'first_place_votes',
        'points_total',
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

     public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}