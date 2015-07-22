<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnType extends \Exception
{
    public function __construct($invalidType, $acceptedTypes, $code = 0)
    {
        if (is_string($invalidType) === true && empty($invalidType) === true) {
            $message = 'Column types cannot be blank, must be a non-empty string.';
        } else {
            $message  = (string) $invalidType . ' is not a valid column type, must one of [ ';
            $message .= implode(' | ', $acceptedTypes) . ']';
        }

        parent::__construct($message, $code);
    }
}
