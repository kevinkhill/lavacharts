<?php

namespace Khill\Lavacharts\Exceptions;

class UndefinedColumnsException extends LavaException
{
    public function __construct()
    {
        parent::__construct('The current DataTable has no defined columns.');
    }
}
