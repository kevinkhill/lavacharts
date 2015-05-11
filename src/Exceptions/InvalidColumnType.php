<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnType extends \Exception
{
    public function __construct($invalidType, $acceptedTypes, $code = 0)
    {
        $message = $invalidType . " is not a valid column type, must one of " . $acceptedTypes;

        parent::__construct($message, $code);
    }
}
