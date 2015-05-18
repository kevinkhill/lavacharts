<?php

namespace Khill\Lavacharts\Filters;



class NumberRange extends Filter
{
    const TYPE = 'NumberRangeFilter';

    public function __construct($columnLabel)
    {
        parent::__construct($columnLabel);
    }
}
