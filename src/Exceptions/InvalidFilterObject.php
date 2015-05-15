<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidFilterObject extends \Exception
{
    public function __construct($invalidFilter, $types)
    {
        $message = "$invalidFilter is not a valid filter, must be one of $types";

        parent::__construct($message);
    }
}
