<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidParamType extends LavaException
{
    public function __construct($invalid, $expected)
    {
        $message =  'Parameter of type ' . $expected . ' was expected, got ' . gettype($invalid) . ' instead';

        parent::__construct($message);
    }
}
