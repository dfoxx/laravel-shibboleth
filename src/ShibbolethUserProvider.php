<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class ShibbolethUserProvider implements UserProvider
{
    protected string $model;
    protected string $identifier_key;

    public function __construct()
    {
        $this->model = config('auth.providers.users.model');
        $this->identifier_key = config('shibboleth.identifier_key');
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        // Not used
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token)
    {
        // Not used
    }

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        $identifier = $_SERVER[$this->identifier_key];

        if (!$identifier) {
            return null;
        }

        $model = $this->createModel();

        $user = $model->newQuery()->where($this->identifier_key, $identifier)->first();

        if (! $user && config('shibboleth.auto_create_users')) {
            $user = new $this->model();

            // map basic headers?

            if (method_exists($user, 'mapShibbolethData')) {
                $user->mapShibbolethData($_SERVER);
            }

            $user->save();
        }

        return $user;
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials)
    {
        return $credentials[$this->identifier_key] === $user->getAuthIdentifier();
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        // Not used
    }

    public function createModel()
    {
        $class = '\\' . ltrim($this->model, '\\');

        return new $class;
    }
}
