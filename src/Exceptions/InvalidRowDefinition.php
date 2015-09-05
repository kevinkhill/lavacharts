<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidRowDefinition extends \Exception
{
    public function __construct($invalidRow, $code = 0)
    {
        $message = gettype($invalidRow) . " is an invalid row definition, must be of type (null|array).";

        parent::__construct($message, $code);
    }
}
