<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Http\Request;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;

class ShibbolethGuard extends SessionGuard
{
    public function __construct(
        string $name,
        UserProvider $provider,
        Session $session,
        Request $request,
        Dispatcher $events = null
    ) {
        parent::__construct($name, $provider, $session, $request);

        if ($events) {
            $this->setDispatcher($events);
        }

        $this->setRequest($request);
    }

    public function shibboleth($field = 'username', $extraConditions = [])
    {
        if ($this->check()) {
            return;
        }

        if ($this->attemptShibboleth($this->getRequest(), $field, $extraConditions)) {
            return;
        }

        return $this->failedShibbolethResponse();
    }

    protected function attemptShibboleth(Request $request, $field, $extraConditions = [])
    {
        return $this->attempt(array_merge(
            $this->shibbolethCredentials($request, $field),
            $extraConditions
        ));
    }

    protected function shibbolethCredentials(Request $request, $field)
    {
        $identifier = null;

        if (app()->environment(['local', 'testing']) && env('APP_USER')) {
            $identifier = env('APP_USER');
        }

        if ($request->server('AUTH_TYPE') === 'shibboleth') {
            $configuredKey = config('shibboleth.identifier', 'SHIB_UID');
            $identifier = $request->server($configuredKey);

            // Fallback to auto-detecting SHIB_UID* keys if specific config fails
            if (empty($identifier)) {
                $pattern = '/^(.+)?' . preg_quote($configuredKey, '/') . '$/';
                $shibboleth_uid_keys = array_values(preg_grep($pattern, array_keys($request->server())));

                if (count($shibboleth_uid_keys)) {
                    $identifier = $request->server($shibboleth_uid_keys[0]);
                }
            }
        }

        return [$field => $identifier, 'auth_type' => 'shibboleth'];
    }

    protected function failedShibbolethResponse()
    {
        logger()->warning('Shibboleth login failed', [
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip(),
            'server' => request()->server(),
        ]);

        abort(401, 'Invalid Shibboleth credentials.');
    }
}
