<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'action',
        'model',
        'model_id',
        'description',
        'user'
    ];
}