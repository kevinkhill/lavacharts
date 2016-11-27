<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnDefinition extends LavaException
{
    public function __construct($invalidCol)
    {
        $msg = gettype($invalidCol);
        $msg.= " is not valid for a column definition, must be an array.";

        parent::__construct($msg);
    }
}
