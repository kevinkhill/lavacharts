<?php

namespace Khill\Lavacharts\Exceptions;

class DashboardNotFound extends LavaException
{
    public function __construct($label)
    {
        $message = "Dashboard('$label') was not found.";

        parent::__construct($message);
    }
}
