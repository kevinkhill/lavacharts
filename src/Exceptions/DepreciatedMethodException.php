<?php

namespace Khill\Lavacharts\Exceptions;

class DepreciatedMethodException extends LavaException
{
    public function __construct($msg)
    {
        parent::__construct($msg);
    }
}
