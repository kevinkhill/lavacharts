<?php

namespace Khill\Lavacharts\Filters;



class Category extends Filter
{
    const TYPE = 'CategoryFilter';

    public function __construct($columnLabel)
    {
        parent::__construct($columnLabel);
    }
}
