<?php namespace Khill\Lavacharts\Exceptions;

class InvalidDate extends \Exception
{
    public function __construct($invalidLabel, $code = 0)
    {
        $message = '"' . $invalidLabel . '" is not a valid date, must be a Carbon instance, datetime string parsable by Carbon.';

        parent::__construct($message, $code);
    }
}
