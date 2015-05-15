<?php

namespace Khill\Lavacharts\Filters;

use \Khill\Lavacharts\Filters\Filter;

class NumberRange extends Filter
{
    const TYPE = 'NumberRangeFilter';

    public function __construct($columnLabel)
    {
        parent::__construct($columnLabel);
    }
}
