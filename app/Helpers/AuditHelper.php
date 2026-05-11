<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditHelper
{
    public static function log($action, $model, $modelId = null, $description = null)
    {
        AuditLog::create([
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'description' => $description,
            'user' => auth()->check() ? auth()->user()->username : 'sistema'
        ]);
    }
}