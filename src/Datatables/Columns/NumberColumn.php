<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class NumberColumn extends Column
{
    const TYPE = 'number';

    public function __construct($label=null)
    {
        parent::__construct($label);
    }
}
