<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidBindings extends LavaException
{
    public function __construct($code = 0)
    {
        $message = 'You must bind ControlWrappers to ChartWrappers, as singles or arrays.';

        parent::__construct($message, $code);
    }
}
