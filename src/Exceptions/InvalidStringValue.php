<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidStringValue extends LavaException
{
    public function __construct()
    {
        parent::__construct('Invalid argument, must be a non-empty string.');
    }
}
