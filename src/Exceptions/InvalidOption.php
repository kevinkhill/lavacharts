<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidOption extends \Exception
{
    public function __construct($option, $choices)
    {
        if (is_string($option) === false) {
            $option = gettype($option);
        }

        parent::__construct("'$option' is not a valid option, must be one of [ ".implode(' | ', $choices).' ]');
    }
}
