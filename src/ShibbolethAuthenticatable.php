<?php

namespace Dfoxx\Shibboleth;

interface ShibbolethAuthenticatable
{
    public function setShibbolethAttributes(array $headers): void;
}
