<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidFunctionParam extends \Exception
{
    public function __construct($badParam, $function, $requiredParam)
    {
        parent::__construct(
            gettype($badParam) . " is not a valid value for $function, must be of type $requiredParam"
        );
    }
}
