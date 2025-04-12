<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shibboleth extends Model
{
    protected $table = 'users_shibboleth';

    protected $fillable = [
        'user_id',
        'uid',
        'eptid',
        'eppn',
        'cpid',
        'session_index',
        'identity_provider',
        'attributes',
    ];


    protected $casts = [
        'attributes' => 'array',
    ];

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected static function booted()
    {
        static::saving(function ($model) {
            if (!is_array($model->attributes['attributes'] ?? null)) {
                return;
            }

            $attributes = $model->attributes['attributes'];
            $promoted = config('shibboleth.promoted_fields', []);

            foreach ($promoted as $key) {
                if (isset($attributes[$key])) {
                    $model->setAttribute($key, $attributes[$key]);
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function scopeWhereEppn($query, string $eppn)
    {
        return $query->where('eppn', $eppn);
    }

    public function scopeWhereUid($query, string $uid)
    {
        return $query->where('uid', $uid);
    }

    public function scopeWhereSessionIndex($query, string $sessionIndex)
    {
        return $query->where('session_index', $sessionIndex);
    }

    public function scopeWhereIdentityProvider($query, string $idp)
    {
        return $query->where('identity_provider', $idp);
    }
}
