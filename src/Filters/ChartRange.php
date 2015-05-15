<?php

namespace Khill\Lavacharts\Filters;

use \Khill\Lavacharts\Filters\Filter;

class ChartRange extends Filter
{
    const TYPE = 'ChartRangeFilter';

    public function __construct()
    {
        parent::__construct($columnLabel);
    }
}
