<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidJson extends \Exception
{
    public function __construct()
    {
        parent::__construct("There was an error decoding the JSON.");
    }
}
