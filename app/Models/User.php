<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Exceptions\CustomException;
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

    /**
     * @throws CustomException
     */
    public function getClientUsersWithFilterAndOrderBy(array $request)
    {
        $query = "";
        $i = 0;
        if ($request['order_by'] != 'asc' && $request['order_by'] != 'desc') {
            throw new CustomException('invalid order_by param');
        }

        $this->checkField($request['field'], 'unsupportable field for sort: ' . $request['field']);

        foreach ($request['filter'] as $key => $value) {
            $this->checkField($key, "$key field unsupportable for filter");
            if ($i === 0) {
                if ($value === null) {
                    $query .= "$key isnull";
                }

                if ($key === 'other_data') {
                    $value = $this->searchInJson($value, 'failed to encode field: ' . $key);

                    $query .= "$key #>> '{}' ilike '$value'";
                }

                if ($value != null && $key != 'other_data') {
                    $query .= "$key ilike '%$value%'";
                }
            }

            if ($i > 0) {
                if ($value === null) {
                    $query .= " and $key isnull";
                }
                if ($key === 'other_data') {
                    $value = $this->searchInJson($value, 'failed to encode field: ' . $key);

                    $query .= " and $key #>> '{}' ilike '$value'";
                }

                if ($value != null && $key != 'other_data') {
                    $query .= " and $key ilike '%$value%'";
                }
            }
            $i++;
        }
        return $this->joinProfile($request['field'], $request['order_by'])->whereRaw($query)->get();
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

    /**
     * @throws CustomException
     */
    public function checkField(string $field, string $errorMessage)
    {
        match ($field) {
            'id', 'login', 'created_at', 'updated_at', 'first_name', 'last_name', 'address', 'other_data' => null,
            default => throw new CustomException($errorMessage)
        };
    }

    /**
     * @throws CustomException
     */
    public function searchInJson(array $jsonArr, string $errMessage): string
    {
        if ($jsonArr === []) {
            throw new CustomException($errMessage);
        }

        foreach ($jsonArr as $k => $v) {
            if ($v === null) {
                $jsonArr[$k] = "null";
                continue;
            }
            $jsonArr[$k] = "%$v%";
        }

        $jsonStr = json_encode($jsonArr);

        if ($jsonStr === false) {
            throw new CustomException($errMessage);
        }

        return $jsonStr;
    }

    public function joinProfile(string $field, string $orderBy)
    {
        return User::select([
            'users.id', 'users.login', 'users.created_at', 'users.updated_at'
        ])->leftJoin('profiles', function (JoinClause $join) {
            $join->on('profiles.user_id', '=', 'users.id');
        })->addSelect(['profiles.first_name', 'profiles.last_name', 'profiles.address', 'profiles.other_data'])
            ->orderBy($field, $orderBy);
    }
}
