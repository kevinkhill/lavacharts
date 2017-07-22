<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidFormatType extends LavaException
{
    public function __construct($badFormat)
    {
        parent::__construct($badFormat . ' is not a valid format');
    }
}
