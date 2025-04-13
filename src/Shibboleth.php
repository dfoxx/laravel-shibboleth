<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shibboleth extends Model
{
    use SoftDeletes;

    protected $table = 'users_shibboleth';

    protected $fillable = [
        'user_id',
        'uid',
        'eptid',
        'eppn',
        'cpid',
        'session_index',
        'identity_provider',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected static function booted()
    {
        static::saving(function ($model) {
            if (!is_array($model->data ?? null)) {
                return;
            }

            $data = $model->data;
            $promoted = config('shibboleth.promoted_fields', []);

            foreach ($promoted as $key) {
                if (isset($data[$key])) {
                    $model->setAttribute($key, $data[$key]);
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
