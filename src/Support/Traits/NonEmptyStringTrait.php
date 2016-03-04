<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait for checking if a parameter is a string and not empty
 *
 * @since  3.1.0
 */
trait NonEmptyStringTrait
{
    /**
     * Checks if a variable is a string and not empty.
     *
     * @param  string $var String to check
     * @return \Khill\Lavacharts\Values\Label
     */
    public function nonEmptyString($var)
    {
        return (is_string($var) && strlen($var) > 0);
    }
}
