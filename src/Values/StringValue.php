<?php

namespace Khill\Lavacharts\Values;
use Khill\Lavacharts\Exceptions\InvalidString;
use Khill\Lavacharts\Exceptions\InvalidStringValue;

/**
 * Class StringValue
 *
 * Creates a new String value object while checking if it is a non empty and actually a string.
 *
 * @package   Khill\Lavacharts\Values
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2016, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class StringValue
{
    /**
     * @var string
     */
    private $value;

    /**
     * StringValue constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        if (is_string($value) === false || empty($value) === true) {
            throw new InvalidStringValue($value);
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
