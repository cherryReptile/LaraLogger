<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getClientUsersWithFilterAndOrderBy(array $request): Collection
    {
        $users = User::orderBy($request['field'], $request['order_by']);
        foreach ($request['filter'] as $key => $value) {
            if ($key === 'first_name' || $key === 'last_name' || $key === 'address') {
                $users = $users->whereHas('profile', function (Builder $query) use ($key, $value){
                    $query->where($key, 'ilike', "%$value%");
                });
                continue;
            }
            $users = $users->where($key, 'ilike', "%$value%");
        }
        return $users->get()->whereNotNull('profile');
    }

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(Provider::class, 'users_providers');
    }

    public function providersData(): HasMany
    {
        return $this->hasMany(ProvidersData::class, 'user_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class, 'user_id');
    }
}
