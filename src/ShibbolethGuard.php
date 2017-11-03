<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Http\Response;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Auth\StatefulGuard;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class ShibbolethGuard extends SessionGuard
{

    /**
     * Attempt to authenticate using Shibboleth.
     *
     * @param  string  $field
     * @param  array  $extraConditions
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function shibboleth($field = 'unity_id', $extraConditions = [])
    {
        if ($this->check()) {
            return;
        }

        // If a username is set on the HTTP basic request, we will return out without
        // interrupting the request lifecycle. Otherwise, we'll need to generate a
        // request indicating that the given credentials were invalid for login.
        if ($this->attemptShibboleth($this->getRequest(), $field, $extraConditions)) {
            return;
        }

        return $this->failedShibbolethResponse();
    }

    /**
     * Attempt to authenticate using Shibboleth.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  string  $field
     * @param  array  $extraConditions
     * @return bool
     */
    protected function attemptShibboleth(Request $request, $field, $extraConditions = [])
    {
        return $this->attempt(array_merge(
            $this->shibbolethCredentials($request, $field), $extraConditions
        ));
    }

    /**
     * Get the credential array for a request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  string  $field
     * @return array
     */
    protected function shibbolethCredentials(Request $request, $field)
    {
        // If on local environment use .env value to mock server variable
         if (\App::environment() == 'local') {
            $identifier = config('services.shib.default_user');
        }

        if ($request->server('AUTH_TYPE') == 'shibboleth') {
            $identifier = $request->server('SHIB_UID') ?: $request->server('REDIRECT_SHIB_UID');
        }

        return [$field => $identifier, 'auth_type' => 'shibboleth'];
    }

    /**
     * Get the response for Shibboleth.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function failedShibbolethResponse()
    {
        // Return a 401 response
        abort(401, 'Invalid credentials.');
    }
}
