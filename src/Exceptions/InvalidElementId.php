<?php namespace Khill\Lavacharts\Exceptions;

class InvalidElementId extends \Exception
{
    public function __construct($invalidElementId, $code = 0)
    {
        $message = gettype($invalidElementId) . ' is not a valid HTML Element ID, must be a non-empty string.';

        parent::__construct($message, $code);
    }
}
