<?php namespace Khill\Lavacharts\Exceptions;

class ChartNotFound extends \Exception
{
    public function __construct($label, $code = 0)
    {
        $message = "Chart ('$label') was not found.";

        parent::__construct($message, $code);
    }
}
