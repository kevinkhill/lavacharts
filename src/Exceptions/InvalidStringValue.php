<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidStringValue extends LavaException
{
    public function __construct($what)
    {
        if (empty($badString)) {
            $message = $what . ' must be a non empty string';
        } else {
            $message = 'Bad type for ' . $what . ', must be a non-empty string.';
        }

        parent::__construct($message);
    }
}
