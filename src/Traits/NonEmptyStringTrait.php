<?php

namespace Khill\Lavacharts\Traits;

trait NonEmptyStringTrait
{
    /**
     * Checks if variable is a non-empty string
     *
     * @param  string $var
     * @return bool
     */
    protected function nonEmptyString($var)
    {
        if (is_string($var) && strlen($var) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
