<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidDateTimeFormat extends LavaException
{
    public function __construct($badFormat)
    {
        parent::__construct(sprintf(
            '"%s" is not a valid value for DateTimeFormat.',
            $badFormat
        ));
    }
}
