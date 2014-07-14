<?php namespace Khill\Lavacharts\Exceptions;

class InvalidLabel extends \Exception
{
    public function __construct($invalidLabel, $code = 0)
    {
        $message = '"' . $invalidLabel . '" is not a valid label, must be type (string).';

        parent::__construct($message, $code);
    }
}
