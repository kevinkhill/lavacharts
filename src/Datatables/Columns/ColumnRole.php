<?php

namespace Khill\Lavacharts\Datatables\Columns;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidColumnRole;

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
        if (Utils::nonEmptyStringInArray($type, $this->roleTypes) === false) {
            throw new InvalidColumnRole($type, $this->roleTypes);
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
