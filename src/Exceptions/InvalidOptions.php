<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidOptions extends LavaException
{
    public function __construct($invalidOptions)
    {
        $type = gettype($invalidOptions);

        parent::__construct("Cannot create options with ($type), must be an array");
    }
}
