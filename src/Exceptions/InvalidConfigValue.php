<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidConfigValue extends LavaException
{
    public function __construct($function, $requiredType, $extra = '')
    {
        $message  = "The value for $function must be of type ($requiredType).";

        if ($extra !== '') {
            $message .= ' ' . $extra;
        }

        parent::__construct($message);
    }
}
