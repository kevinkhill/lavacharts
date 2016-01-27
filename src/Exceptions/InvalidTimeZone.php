<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidTimeZone extends \Exception
{
    public function __construct($badZone)
    {
        if (is_string($badZone)) {
            $message = "$badZone is not a valid timezone.";
        } else {
            $message = gettype($badZone) . ' is not a valid timezone.';
        }

        parent::__construct($message);
    }
}
