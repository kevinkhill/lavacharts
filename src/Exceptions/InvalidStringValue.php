<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidDateTimeFormat extends LavaException
{
    public function __construct($badString)
    {
        if (is_string($badString) && empty($badString)) {
            $message = 'Must be a non empty string which follows standard DateTime Formatting';
        } else {
            $message = gettype($badString) . ' is invalid, must be a datetime format string which Carbon can use.';
        }

        parent::__construct($message);
    }
}
