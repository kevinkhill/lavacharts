<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidEvent extends \Exception
{
    public function __construct($invalidEvent, $validEvents)
    {
        $message  = $invalidEvent . " is not a valid event, must a one of ";
        $message .= '[ ' . implode(' | ', $validEvents) . ' ]';

        parent::__construct($message);
    }
}
