<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidDivDimensions extends LavaException
{
    public function __construct($code = 0)
    {
        $message = "Invalid div dimensions, array('height' => (int), 'width' => (int))";

        parent::__construct($message, $code);
    }
}
