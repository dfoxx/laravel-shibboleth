<?php

namespace Dfoxx\Shibboleth;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        $config = config('shibboleth');

        $identifier = null;

        if (app()->environment(['local', 'testing']) && $config['user']) {
            $identifier = $config['user'];
        }

        $identifier = $request->server($config['server_key']);

        if (! $identifier) {
            abort(401, 'Shibboleth identifier missing.');
        }

        $model = config('auth.providers.users.model');
        $user = $model::where($config['identifier_key'], $identifier)->first();

        if (! $user && $config['auto_create_users']) {
            $user = new $model;
            if (method_exists($user, 'mapShibbolethData')) {
                $user->mapShibbolethData($request->server());
                $user->save();
            } else {
                abort(403, 'Shibboleth auto create user turned off.');
            }
        }

        if (! $user) {
            abort(401, 'Shibboleth user not found.');
        }

        Auth::login($user);

        return $next($request);
    }
}
