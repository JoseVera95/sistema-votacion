<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable = [
        'grado',
        'cedula',
        'nombres',
        'apellidos',
        'foto',
        'has_voted',
        'voted_at',
    ];

    protected $casts = [
        'has_voted' => 'boolean',
        'voted_at' => 'datetime',
    ];
    
public function votes()
    {
        return $this->hasMany(\App\Models\Vote::class);
    }
}