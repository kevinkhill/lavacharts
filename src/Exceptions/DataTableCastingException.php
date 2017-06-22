<?php

namespace Khill\Lavacharts\Exceptions;

class DataTableCastingException extends LavaException
{
    public function __construct($class, $missingMethod)
    {
        $message = sprintf('Failed to cast %1$s as a DataTable because %1$s#%s() is not defined.',
            get_class($class),
            $missingMethod
        );

        parent::__construct($message);
    }
}
