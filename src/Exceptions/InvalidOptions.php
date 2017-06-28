<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidOptions extends LavaException
{
    public function __construct($invalidOptions)
    {
        parent::__construct(sprintf(
            'Cannot create options with "%s", must be an array',
            gettype($invalidOptions)
        ));
    }
}
