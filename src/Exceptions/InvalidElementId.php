<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidElementId extends LavaException
{
    public function __construct($invalidElementId = '', $code = 0)
    {
        if (is_string($invalidElementId)) {
            $message = 'ElementIds cannot be blank, must be a non-empty string.';
        } else {
            $message = gettype($invalidElementId) . ' is not a valid HTML element ID, must be a non-empty string.';
        }

        parent::__construct($message, $code);
    }
}
