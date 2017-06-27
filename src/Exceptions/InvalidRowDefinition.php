<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidRowDefinition extends LavaException
{
    /**
     * InvalidRowDefinition constructor.
     *
     * @param mixed $invalidRow
     */
    public function __construct($invalidRow)
    {
        $message = gettype($invalidRow) . " is an invalid row definition, must be of type (null|array).";

        parent::__construct($message);
    }
}
