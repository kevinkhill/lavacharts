<?php

namespace Khill\Lavacharts\Exceptions;

use \Khill\Lavacharts\Charts\Chart;

class DataTableNotFound extends \Exception
{
    public function __construct(Chart $chart, $code = 0)
    {
        $message = $chart::TYPE . '(' . $chart->getLabel() . ') has no DataTable.';

        parent::__construct($message, $code);
    }
}
