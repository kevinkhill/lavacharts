<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class TimeOfDayColumn extends DateColumn
{
    const TYPE = 'timeofday';

    public function __construct(Label $label=null)
    {
        parent::__construct($label);
    }
}
