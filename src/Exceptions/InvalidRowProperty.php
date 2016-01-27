<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidRowProperty extends \Exception
{
    public function __construct()
    {
        $message = 'Invalid row property, array with keys type (string) with values [ v | f | p ] ';

        parent::__construct($message, 0);
    }
}
