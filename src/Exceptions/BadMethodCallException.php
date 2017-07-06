<?php

namespace Khill\Lavacharts\Exceptions;

class BadMethodCallException extends LavaException
{
    public function __construct($object, $method)
    {
        $message = '"%s" is not a valid method on "%s"';

        parent::__construct(sprintf($message, $method, get_class($object)));
    }
}
