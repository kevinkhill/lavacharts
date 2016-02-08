<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnType extends LavaException
{
    public function __construct($invalidType, $acceptedTypes, $code = 0)
    {
        if (is_string($invalidType)) {
            $message = "$invalidType is not a valid column type.";
        } else {
            $message  = gettype($invalidType) . ' is not a valid column type.';
        }

        $message .= ' Must one of [ ' . implode(' | ', $acceptedTypes) . ' ]';

        parent::__construct($message, $code);
    }
}
