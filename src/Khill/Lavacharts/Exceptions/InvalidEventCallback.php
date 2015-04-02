<?php namespace Khill\Lavacharts\Exceptions;

class InvalidEventCallback extends \Exception
{
    public function __construct($invalidEventCallback = null, $code = 0)
    {
        $message = gettype($invalidEventCallback) . " is an invalid event callback, must a (string) of a previously defined javascript function in page.";

        parent::__construct($message, $code);
    }
}
