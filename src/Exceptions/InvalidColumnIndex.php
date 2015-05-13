<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnIndex extends \Exception
{
    public function __construct($columnIndex, $count)
    {
        if (is_int($columnIndex)) {
            $message = $columnIndex .' is an invalid column index, must be 0 - '.($count-1).'.';
        } else {
            $message = gettype($columnIndex) . ' is an invalid column index, must an (int), 0 - '.($count-1).'.';
        }

        parent::__construct($message);
    }
}
