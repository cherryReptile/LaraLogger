<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'other_data'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}