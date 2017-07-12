<?php

namespace Khill\Lavacharts\Exceptions;

use Khill\Lavacharts\Charts\ChartFactory;

class InvalidChartType extends LavaException
{
    public function __construct($type)
    {
        $message = '"%s" is not a valid chart type, must be one of %s';

        parent::__construct(sprintf(
            $message,
            $type,
            implode(' | ', ChartFactory::TYPES)
        ));
    }
}
