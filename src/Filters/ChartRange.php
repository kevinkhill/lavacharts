<?php

namespace Khill\Lavacharts\Filters;



class ChartRange extends Filter
{
    const TYPE = 'ChartRangeFilter';

    public function __construct($columnLabel)
    {
        parent::__construct($columnLabel);
    }
}
