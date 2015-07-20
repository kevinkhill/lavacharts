<?php

namespace Khill\Lavacharts\Values;

class Options
{
    private $defaults = [];

    public function __construct($defaults)
    {
        $this->defaults = $defaults;
    }

    public function set($options)
    {
        array_merge($this->defaults, $options);
    }
}
