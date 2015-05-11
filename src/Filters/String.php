<?php

namespace Khill\Lavacharts\Filters;

use \Khill\Lavacharts\Filters\Filter;

class String extends Filter
{
    const TYPE = 'StringFilter';

    public function __construct()
    {
        parent::__construct($columnLabel);
    }
}
