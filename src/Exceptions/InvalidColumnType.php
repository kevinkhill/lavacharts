<?php

namespace Khill\Lavacharts\Exceptions;

use Khill\Lavacharts\DataTables\Columns\Column;

class InvalidColumnType extends LavaException
{
    public function __construct($badColumn)
    {
        if (is_string($badColumn)) {
            $message = "$badColumn is not a valid column type.";
        } else {
            $message = gettype($badColumn) . ' is not a valid column type.';
        }

        $message .= ' Must one of [ ' . implode(' | ', Column::TYPES) . ' ]';

        parent::__construct($message);
    }
}
