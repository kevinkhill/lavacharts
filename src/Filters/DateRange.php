<?php

namespace Khill\Lavacharts\Filters;

use \Khill\Lavacharts\Filters\Filter;

class DateRange extends Filter
{
    const TYPE = 'DateRangeFilter';

    public function __construct()
    {
        parent::__construct($columnLabel);
    }
}
