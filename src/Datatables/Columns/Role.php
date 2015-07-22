<?php

namespace Khill\Lavacharts\Datatables;

use \Khill\Lavacharts\Values\String;

class Role
{
    private $validRoles = [
        'annotation',
        'annotationText',
        'certainty',
        'emphasis',
        'interval',
        'scope',
        'style',
        'tooltip'
    ];

    public function __construct($type)
    {
        $this->type = new String($type);
    }

    public function getRole()
    {
        return $this->role;
    }
}
