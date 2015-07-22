<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class DateColumn extends Column
{
    const TYPE = 'date';

    public function __construct(Label $label, Label $id, Format $format = null)
    {
        parent::__construct($label, $id, $format);
    }

}
