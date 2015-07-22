<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class StringColumn extends Column
{
    const TYPE = 'string';

    public function __construct(Label $label)
    {
        parent::__construct($label);
    }
}
