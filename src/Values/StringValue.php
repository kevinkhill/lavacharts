<?php

namespace Khill\Lavacharts\Values;

use Khill\Lavacharts\Exceptions\InvalidStringValue;

/**
 * Class StringValue
 *
 * Creates a new String value object while checking if it is non empty and actually a string.
 *
 * @package   Khill\Lavacharts\Values
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class StringValue implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $value;

    /**
     * StringValue constructor.
     *
     * @param  string $value
     * @throws \Khill\Lavacharts\Exceptions\InvalidStringValue
     */
    public function __construct($value)
    {
        if (is_string($value) === false || empty($value) === true) {
            throw new InvalidStringValue;
        }

        $this->value = $value;
    }

    /**
     * Check a value if is string and not empty without creating
     * an instance.
     *
     * @param  string $value
     * @return bool
     */
    public static function isNonEmpty($value)
    {
        try {
            new self($value);

            return true;
        } catch (InvalidStringValue $e) {
            return false;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}
