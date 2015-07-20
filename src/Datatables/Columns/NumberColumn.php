<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Formats\Format;

class NumberColumn extends DataColumn
{
    const TYPE = 'number';

    public function __construct($label, $id)
    {
        $label  = new Label($label);
        $id     = new Label($id);
        $format = $format;

        parent::__construct($label, $id, $format);
    }

    public function jsonSerialize()
    {
        return [
            'type'  => self::TYPE,
            'label' => (string) $this->label,
            'id'    => $this->id,
        ];
    }
}
