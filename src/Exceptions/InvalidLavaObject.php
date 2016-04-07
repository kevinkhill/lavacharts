<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidLavaObject extends LavaException
{
    public function __construct($badLavaObject, $code = 0)
    {
        $message = "'$badLavaObject' is not a valid Lavachart object.";

        parent::__construct($message, $code);
    }
}
