<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class StringColumn extends DataColumn
{
    const TYPE = 'string';

    public function __construct($label, $id, $index, Format $format=null)
    {
        $label  = new Label($label);
        $id     = new Label($id);
        $index  = $index;
        $format = $format;

        parent::__construct($label, $id, $index, $format);
    }
}
