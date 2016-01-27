<?php

namespace Khill\Lavacharts\Exceptions;

class ChartNotFound extends \Exception
{
    public function __construct($type, $label, $code = 0)
    {
        $message = "$type('$label') was not found.";

        parent::__construct($message, $code);
    }
}
