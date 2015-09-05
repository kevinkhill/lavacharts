<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidEventCallback extends \Exception
{
    public function __construct($invalidEventCallback)
    {
        $message  = gettype($invalidEventCallback) . ' is an invalid event callback, ';
        $message .= 'must a non-empty (string) of a previously defined javascript function.';

        parent::__construct($message);
    }
}
