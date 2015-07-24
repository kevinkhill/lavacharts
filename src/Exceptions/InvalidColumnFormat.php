<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnFormat extends \Exception
{
    public function __construct($format)
    {
        parent::__construct(
            '"'. (string) $format . '" is not a valid format.'
        );
    }
}
