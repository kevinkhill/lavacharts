<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidUIProperty extends \Exception
{
    public function __construct($rejectedProp, $acceptedProps)
    {
        natcasesort($acceptedProps);

        $message  = $rejectedProp . ' is not a valid UI property.';
        $message .= ' Must be one of [ ' . implode(' | ', $acceptedProps) . ' ]';

        parent::__construct($message);
    }
}
