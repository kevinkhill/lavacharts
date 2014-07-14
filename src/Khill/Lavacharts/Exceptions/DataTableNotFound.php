<?php namespace Khill\Lavacharts\Exceptions;

class DataTableNotFound extends \Exception
{
    public function __construct($chart, $code = 0)
    {
        $message = "$chart->type('$chart->label') has no DataTable.";

        parent::__construct($message, $code);
    }
}
