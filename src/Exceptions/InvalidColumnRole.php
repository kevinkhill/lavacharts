<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnRole extends \Exception
{
    public function __construct($role, $validRoles)
    {
        parent::__construct(
            '"'. (string) $role . '" is not a valid role, must be one of [ ' . implode(' | ', $validRoles) . ' ]'
        );
    }
}
