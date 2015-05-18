<?php

namespace Khill\Lavacharts\Filters;



class String extends Filter
{
    const TYPE = 'StringFilter';

    public function __construct()
    {
        parent::__construct($columnLabel);
    }
}
