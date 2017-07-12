<?php

namespace Khill\Lavacharts\Exceptions;

class InvalidJson extends LavaException
{
    public function __construct()
    {
        parent::__construct(json_last_error_msg());
    }
}
