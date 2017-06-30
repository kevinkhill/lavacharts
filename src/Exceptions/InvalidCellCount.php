<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidCellCount extends LavaException
{
    public function __construct($columnCount)
    {
        $message = 'The number of cells per row cannot exceed the number of Columns [%s].';

        parent::__construct(sprintf($message, $columnCount));
    }
}
