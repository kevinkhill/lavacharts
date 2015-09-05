<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidDate extends \Exception
{
    public function __construct($badString)
    {
        $message = 'Must be a Carbon instance or a datetime string able to be parsed by Carbon.';

        parent::__construct($message);
    }
}
