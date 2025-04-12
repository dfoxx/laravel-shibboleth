<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class ShibbolethUserProvider implements UserProvider
{
    protected string $model;

    public function __construct(string $model)
    {
        $this->model = $model;
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
        $identifier = $_SERVER[config('shibboleth.identifier')];

        if (!$identifier) {
            return null;
        }

        $model = $this->createModel();

        $user = $model->newQuery()->where(config('shibboleth.identifier'), $identifier)->first();

        if (! $user && config('shibboleth.auto_create_users')) {
            $user = new $this->model();

            if (method_exists($user, 'mapShibbolethData')) {
                $user->mapShibbolethData($_SERVER);
                $user->save();
            } else {
                throw new \LogicException("User model must implement mapShibbolethData()");
            }
        }

        return $user;
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials)
    {
        $identifier = $user->getAuthIdentifierName();

        return $credentials[$identifier] === $user->getAuthIdentifier()
            && $credentials['auth_type'] === 'shibboleth';
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
