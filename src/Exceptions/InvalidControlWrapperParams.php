<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidControlWrapperParams extends \Exception
{
    public function __construct()
    {
        $message  = "Invalid ControlWrapper parameters, must be (Filter, string";

        parent::__construct($message);
    }
}
