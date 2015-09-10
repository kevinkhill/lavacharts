<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnRole extends \Exception
{
    public function __construct($invalidRole, $validRoles)
    {
        if (is_string($invalidRole)) {
            $message = "$invalidRole is not a valid column role, must a one of ";
        } else {
            $message = gettype($invalidRole) . ' is not a valid column role, must a one of ';
        }

        $message .= '[ ' . implode(' | ', $validRoles) . ' ]';

        parent::__construct($message);
    }
}
