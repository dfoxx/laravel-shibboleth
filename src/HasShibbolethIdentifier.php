<?php

namespace Dfoxx\Shibboleth;

trait HasShibbolethIdentifier
{
    public function getAuthIdentifierName(): string
    {
        return config('shibboleth.identifier_key');
    }

    public function getAuthIdentifier(): mixed
    {
        $column = $this->getAuthIdentifierName();
        return $this->{$column};
    }
}
