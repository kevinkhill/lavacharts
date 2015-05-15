<?php

namespace Khill\Lavacharts\Filters;

use \Khill\Lavacharts\Filters\Filter;

class Category extends Filter
{
    const TYPE = 'CategoryFilter';

    public function __construct()
    {
        parent::__construct($columnLabel);
    }
}
