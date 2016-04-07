<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidDateTimeString extends LavaException
{
    public function __construct($badString)
    {
        if (is_string($badString) && empty($badString)) {
            $message = 'Must be a non empty datetime string which Carbon can parse.';
        } else {
            $message = gettype($badString) . ' is invalid, must be a datetime string which Carbon can parse.';
        }

        parent::__construct($message);
    }
}
