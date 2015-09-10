<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidChartWrapperParams extends \Exception
{
    public function __construct()
    {
        $message  = "Invalid ChartWrapper parameters, must be (Chart, string)";

        parent::__construct($message);
    }
}
