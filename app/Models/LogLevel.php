<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogLevel extends Model
{
    use HasFactory;

    public function log(): BelongsTo
    {
        return $this->belongsTo(Log::Class, 'log_level_id', 'id');
    }
}
