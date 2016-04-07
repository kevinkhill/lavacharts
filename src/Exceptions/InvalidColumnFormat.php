<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnFormat extends LavaException
{
    public function __construct($format)
    {
        parent::__construct(
            '"'. (string) $format . '" is not a valid format.'
        );
    }
}
