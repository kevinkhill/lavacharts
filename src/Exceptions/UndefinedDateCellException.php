<?php

namespace Khill\Lavacharts\Exceptions;

class UndefinedDateCellException extends LavaException
{
    public function __construct($datetime, $format = '')
    {
        if (empty($format)) {
            $msg = 'Carbon failed to parse "%s" with no format.';
        } else {
            $msg = 'Carbon failed to parse "%s" with the format "%s".';
        }

        parent::__construct(sprintf($msg, $datetime, $format));
    }
}
