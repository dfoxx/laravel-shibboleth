<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Http\Request;

trait HasShibbolethData
{
    public function shibboleth()
    {
        return $this->hasOne(Shibboleth::class);
    }

    public function shib(string $key, $default = null)
    {
        return $this->shibboleth?->data[$key] ?? $default;
    }

    public function mapShibbolethData(Request $request)
    {
        $data = [];

        $map = config('shibboleth.map');

        foreach ($map as $key => $headerName) {
            $value = $request->server($headerName) ?? $request->header($headerName);

            if (!is_null($value)) {
                $data[$key] = $value;
            }
        }

        $this->shibboleth()->updateOrCreate(
            ['user_id' => $this->getAuthIdentifier()],
            ['data' => $data]
        );

        return $data;
    }
}
