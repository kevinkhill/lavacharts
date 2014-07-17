<?php namespace Khill\Lavacharts\Exceptions;

class InvalidColumnDefinition extends \Exception
{
    public function __construct($invalidCol, $code = 0)
    {
        $message = gettype($invalidCol) . " is an invalid column definition, must be an array with 1-3 strings defining type, desc, id";

        parent::__construct($message, $code);
    }
}
