<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidDataTable extends LavaException
{
    public function __construct($badData)
    {
        $message = gettype($badData) . ' is not a valid Table.';

        parent::__construct($message);
    }
}
