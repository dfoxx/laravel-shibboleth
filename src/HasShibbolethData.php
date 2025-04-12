<?php

namespace Dfoxx\Shibboleth;

trait HasShibbolethData
{
    public function shibboleth()
    {
        return $this->hasOne(Shibboleth::class);
    }

    public function shib(string $key, $default = null)
    {
        return $this->shibboleth?->attributes[$key] ?? $default;
    }

    public function setShibbolethAttributes(array $headers)
    {
        //
    }
}
