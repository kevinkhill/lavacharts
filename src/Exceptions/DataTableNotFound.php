<?php

namespace Khill\Lavacharts\Exceptions;

class DataTableNotFound extends \Exception
{
    public function __construct($chart, $code = 0)
    {
        $message = $chart::TYPE . '(' . $chart->getLabel() . ') has no DataTable.';

        parent::__construct($message, $code);
    }
}
