<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidLabelException extends LavaException
{
    public function __construct($invalidLabel = '')
    {
        $message = 'Labels cannot be blank, must be a non-empty string.';

        if (! is_string($invalidLabel)) {
            $message = gettype($invalidLabel) . ' is not a valid label, must be a non-empty string.';
        }

        parent::__construct($message);
    }
}
