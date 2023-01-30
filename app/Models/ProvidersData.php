<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvidersData extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'username',
        'provider_id'
    ];
}
