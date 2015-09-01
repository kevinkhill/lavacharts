<?php

namespace Khill\Lavacharts\Exceptions;

class FailedCarbonParsing extends \Exception
{
    public function __construct($badString)
    {
        $message = (string) $badString . ' failed to be parsed by Carbon.';

        parent::__construct($message);
    }
}
