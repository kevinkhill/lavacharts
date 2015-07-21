<?php

namespace Khill\Lavacharts\Datatables\Rows;

class NullRow extends Row
{
    public function __construct($numOfCols)
    {
        for ($a = 0; $a < $numOfCols; $a++) {
            $tmp[] = null;
        }

        parent::__construct($tmp);
    }
}
