<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'file',
        'class',
        'changed_properties',
        'all_properties',
        'calling_line',
        'level'
    ];

    protected $table = 'user_logs';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::Class);
    }
}
