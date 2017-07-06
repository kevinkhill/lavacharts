<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidArgumentException extends LavaException
{
    public function __construct($actual, $expected)
    {
        $message = '"%s" is not a valid argument, expected "%s"';

        parent::__construct(sprintf($message, gettype($actual), $expected));
    }
}
