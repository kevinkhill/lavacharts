<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException
{
    public function __construct($actual, $expected)
    {
        parent::__construct(sprintf(
            '"%s" is not a valid argument, expected "%s"', $actual, $expected
        ));
    }
}
