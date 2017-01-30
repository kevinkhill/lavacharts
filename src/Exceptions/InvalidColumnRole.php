<?php

namespace Khill\Lavacharts\Exceptions;

use Khill\Lavacharts\Values\Role;

class InvalidColumnRole extends LavaException
{
    public function __construct($invalidRole)
    {
        if (is_string($invalidRole)) {
            $message = "'$invalidRole' is not a valid column role, must be one of ";
        } else {
            $message = gettype($invalidRole) . ' is not a valid column role, must one of ';
        }

        $message .= '[ ' . implode(' | ', Role::$roles) . ' ]';

        parent::__construct($message);
    }
}
