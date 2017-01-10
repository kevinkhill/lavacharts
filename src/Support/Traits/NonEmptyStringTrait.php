<?php

namespace Khill\Lavacharts\Support\Traits;

/**
 * Trait NonEmptyStringTrait
 *
 * Provides the method for checking if a parameter is a string and not empty
 *
 * @package   Khill\Lavacharts\Support\Traits
 * @since     3.1.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
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
