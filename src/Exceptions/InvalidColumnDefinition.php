<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidColumnDefinition extends LavaException
{
    public function __construct($invalidCol)
    {
        $msg = gettype($invalidCol);
        $msg.= " is an invalid column definition, must be an array with 1-3 strings defining type, desc, id";

        parent::__construct($msg);
    }
}
