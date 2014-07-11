<?php namespace Khill\Lavacharts\Exceptions;

class InvalidDate extends \Exception
{
    public function __construct($invalidLabel, $code = 0)
    {
        $message = '"' . $invalidLabel . '" is not a date, must be a Carbon instance or a datetime string parsable by Carbon.';

        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
