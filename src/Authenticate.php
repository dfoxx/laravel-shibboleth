<?php

namespace Dfoxx\Shibboleth;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthenticateWithShibboleth
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        $identifierKey = config('shibboleth.identifier', 'SHIB_UID');
        $identifierColumn = config('shibboleth.identifier_column', 'uid');
        $identifierValue = $request->server($identifierKey);

        if (! $identifierValue) {
            abort(401, 'Shibboleth identifier missing.');
        }

        $modelClass = config('auth.providers.users.model');
        $user = $modelClass::where($identifierColumn, $identifierValue)->first();

        if (! $user && config('shibboleth.auto_create_users')) {
            $user = new $modelClass;
            if (method_exists($user, 'setShibbolethAttributes')) {
                $user->setShibbolethAttributes($request->server());
                $user->save();
            } else {
                abort(403, 'User model does not support Shibboleth creation.');
            }
        }

        if (! $user) {
            abort(401, 'Shibboleth user not found.');
        }

        Auth::login($user);

        return $next($request);
    }
}
