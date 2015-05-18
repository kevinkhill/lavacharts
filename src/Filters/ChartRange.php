<?php

namespace Khill\Lavacharts\Filters;



class ChartRange extends Filter
{
    const TYPE = 'ChartRangeFilter';

    public function __construct()
    {
        parent::__construct($columnLabel);
    }
}
