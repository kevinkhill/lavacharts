<?php

namespace Khill\Lavacharts\Exceptions;

use Khill\Lavacharts\Dashboards\Filter;

class InvalidFilterType extends LavaException
{
    public function __construct($invalidFilter)
    {
        if (is_string($invalidFilter)) {
            $message = $invalidFilter;
        } else {
            $message = gettype($invalidFilter);
        }

        $message .= ' is not a valid filter, must be one of [ ' . implode(' | ', Filter::TYPES) . ']';

        parent::__construct($message);
    }
}
