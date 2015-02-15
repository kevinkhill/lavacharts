<?php namespace Khill\Lavacharts\Exceptions;

class InvalidDate extends \Exception
{
    public function __construct()
    {
        $message = 'Must be a Carbon instance or a datetime string parsable by Carbon.';

        parent::__construct($message);
    }
}
