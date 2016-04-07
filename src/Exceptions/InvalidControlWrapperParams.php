<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidControlWrapperParams extends LavaException
{
    public function __construct()
    {
        $message  = "Invalid ControlWrapper parameters, must be (Filter, string";

        parent::__construct($message);
    }
}
