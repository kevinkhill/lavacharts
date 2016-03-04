<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidFormatType extends LavaException
{
    public function __construct($badType)
    {
        $message = (string) $badType . ' is not a valid format';

        parent::__construct($message);
    }
}
