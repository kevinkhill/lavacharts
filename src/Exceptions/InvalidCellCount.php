<?php namespace Khill\Lavacharts\Exceptions;

class InvalidCellCount extends \Exception
{
    public function __construct($cellCount, $columnCount, $code = 0)
    {
        $message  = 'Invalid number of cells, must be less than or equal to the number of columns. ';
        $message .= "(cells: $cellCount > columns: $columnCount)";

        parent::__construct($message, $code);
    }
}
