<?php namespace Khill\Lavacharts\Exceptions;

class InvalidColumnIndex extends \Exception
{
    public function __construct($invalidColInd, $code = 0)
    {
        $message = gettype($invalidColInd) . " is an invalid column index, must an (int) of a defined column.";

        parent::__construct($message, $code);
    }
}
