<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidDataTable extends \Exception
{
    public function __construct($InvalidDataTable = null)
    {
        $message = gettype($InvalidDataTable) . ' is not a valid DataTable.';

        parent::__construct($message);
    }
}
