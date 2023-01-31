<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ProvidersData extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'username',
        'provider_id'
    ];

    public static function findByProviderIdAndUsername(int $id, string $username): ProvidersData|null
    {
         return self::whereRaw("provider_id=$id and username='$username'")->first();
    }

    public function addProviderWithData(User $user, Provider $provider, array $data)
    {
        $user->providersData()->create([
            'data' => json_encode($data),
            'username' => $data[$provider->unique_key],
            'provider_id' => $provider->id
        ]);

        DB::table('users_providers')->insert([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
