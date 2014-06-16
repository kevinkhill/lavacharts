<?php namespace Khill\Lavacharts\Exceptions;

class InvalidElementId extends \Exception
{
    public function __construct($invalidLabel, $code = 0)
    {
        $message = '"' . $invalidLabel . '" is not a valid HTML Element ID, must be type (string) and not empty.';

        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
