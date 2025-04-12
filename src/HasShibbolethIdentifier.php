<?php

namespace Dfoxx\Shibboleth;

trait HasShibbolethIdentifier
{
    public function getAuthIdentifierName(): string
    {
        return config('shibboleth.identifier_column');
    }

    public function getAuthIdentifier(): mixed
    {
        $column = $this->getAuthIdentifierName();
        return $this->{$column};
    }
}
