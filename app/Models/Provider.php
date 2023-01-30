<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'user_id',
        'provider_id',
        'created_at',
        'updated_at'
    ];
}
