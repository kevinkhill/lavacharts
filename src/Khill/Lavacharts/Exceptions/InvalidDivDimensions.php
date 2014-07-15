<?php namespace Khill\Lavacharts\Exceptions;

class InvalidDate extends \Exception
{
    public function __construct($invalidDims, $code = 0)
    {
        $message = "Invalid div dimensions, array('height' => (int), 'width' => (int))";

        parent::__construct($message, $code);
    }
}
