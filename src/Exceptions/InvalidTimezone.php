<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidTimeZone extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
