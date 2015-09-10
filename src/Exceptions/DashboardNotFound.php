<?php

namespace Khill\Lavacharts\Exceptions;

class DashboardNotFound extends \Exception
{
    public function __construct($label)
    {
        $message = "Dashboard('$label') was not found.";

        parent::__construct($message);
    }
}
