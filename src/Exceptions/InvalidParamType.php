<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidParamType extends LavaException
{
    public function __construct($invalid, $expected)
    {
        parent::__construct(sprintf(
            'Parameter of type "%s" was expected, got "%s" instead.',
            $expected,
            gettype($invalid)
        ));
    }
}
