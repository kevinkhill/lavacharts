<?php

namespace Khill\Lavacharts\Exceptions;

class UndefinedOptionException extends LavaException
{
    public function __construct($option)
    {
        parent::__construct(sprintf(
            '"%s" does not exist as an option for this object.', $option
        ));
    }
}
