<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidConfigProperty extends LavaException
{
    public function __construct($class, $function, $rejectedProp, $acceptedProps, $code = 0)
    {
        natcasesort($acceptedProps);

        $message  = '"'.$rejectedProp.'" is not a valid property for ' . $class . '->' . $function . ', ';
        $message .= 'must be one of [ ' . implode(' | ', $acceptedProps) . ' ]';

        parent::__construct($message, $code);
    }
}
