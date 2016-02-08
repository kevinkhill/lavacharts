<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidJson extends LavaException
{
    public function __construct()
    {
        parent::__construct("There was an error decoding the JSON.");
    }
}
