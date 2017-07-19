<?php

namespace Khill\Lavacharts\Exceptions;

class BindingException extends LavaException
{
    public function __construct()
    {
        $msg = 'You must bind ControlWrappers to ChartWrappers, as singles or arrays.';

        parent::__construct($msg);
    }
}
