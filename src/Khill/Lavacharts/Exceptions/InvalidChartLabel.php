<?php namespace Khill\Lavacharts\Exceptions;

class InvalidChartLabel extends \Exception
{
    public function __construct($invalidLabel = null, $code = 0)
    {
        $message = gettype($invalidLabel) . ' is not a valid label, must be a non-empty (string).';

        parent::__construct($message, $code);
    }
}
