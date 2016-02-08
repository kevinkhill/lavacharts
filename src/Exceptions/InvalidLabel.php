<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidLabel extends LavaException
{
    public function __construct($invalidLabel = '')
    {
        if (is_string($invalidLabel)) {
            $message = 'Labels cannot be blank, must be a non-empty string.';
        } else {
            $message = gettype($invalidLabel) . ' is not a valid label, must be a non-empty string.';
        }

        parent::__construct($message);
    }
}
