<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class DateTimeColumn extends DateColumn
{
    const TYPE = 'datetime';

    public function __construct(Label $label, Label $id)
    {
        parent::__construct($label, $id);
    }
}
