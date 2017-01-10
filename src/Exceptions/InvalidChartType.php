<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidChartType extends LavaException
{
    public function __construct($invalidChart, array $validCharts)
    {
        $badChart = (string) $invalidChart;

        $message  = "'$badChart' is not a valid chart type, must be one of ";
        $message .= '[ ' . implode(' | ', $validCharts) . ']';

        parent::__construct($message);
    }
}
