<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;

class ShibbolethGuard extends SessionGuard
{
    public function __construct(
        string $name,
        UserProvider $provider,
        Session $session,
        Request $request
    ) {
        parent::__construct($name, $provider, $session, $request);

        $this->setRequest($request);
    }

    public function check()
    {
        // If already authenticated, do nothing extra
        if (parent::check()) {
            return true;
        }

        // Try to log in via Shibboleth headers
        $identifier_key = config('shibboleth.identifier_key');

        // Attempt login
        $this->shibboleth($identifier_key);

        // Return the result of check() again after the attempt
        return parent::check();
    }

    public function shibboleth($identifier_key)
    {
        $request = $this->getRequest();

        if ($this->attemptShibboleth($request, $identifier_key)) {
            return;
        }

        return $this->failedShibbolethResponse($request);
    }

    protected function attemptShibboleth(Request $request, $identifier_key)
    {
        return $this->attempt($this->shibbolethCredentials($request, $identifier_key));
    }

    protected function shibbolethCredentials(Request $request, $identifier_key)
    {
        $identifier = null;

        if (app()->environment('local') && config('shibboleth.user')) {
            $identifier = config('shibboleth.user');
        }

        $identifier = $request->server($identifier_key);

        // Fallback to auto-detecting SHIB_UID* keys if specific config fails
        if (empty($identifier)) {
            $pattern = '/^(.+)?' . preg_quote($identifier_key, '/') . '$/';
            $shibboleth_uid_keys = array_values(preg_grep($pattern, array_keys($request->server())));

            if (count($shibboleth_uid_keys)) {
                $identifier = $request->server($shibboleth_uid_keys[0]);
            }
        }

        return [$identifier_key => $identifier];
    }

    protected function failedShibbolethResponse(Request $request)
    {
        logger()->warning('Shibboleth login failed', [
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'server' => $request->server(),
        ]);

        abort(401, 'Invalid Shibboleth credentials.');
    }
}
