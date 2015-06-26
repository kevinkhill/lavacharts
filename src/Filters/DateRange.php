<?php

namespace Khill\Lavacharts\Filters;



class DateRange extends Filter
{
    const TYPE = 'DateRangeFilter';

    public function __construct($columnLabel)
    {
        parent::__construct($columnLabel);
    }
}
