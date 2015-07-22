<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Values\String;

class ColumnRole implements \JsonSerializable
{
    private $roleTypes = [
        'annotation',
        'annotationText',
        'certainty',
        'emphasis',
        'interval',
        'scope',
        'style',
        'tooltip'
    ];

    private $type;

    public function __construct($type)
    {
        if (in_array($type, $this->$roleTypes) === false) {
            throw new InvalidRoleType($type, $this->$roleTypes);
        }

        $this->type = $type;
    }

    public function __toString()
    {
        return $this->type;
    }

    public function jsonSerialize()
    {
        return ['role' => $this->type];
    }
}
