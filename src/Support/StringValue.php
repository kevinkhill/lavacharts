<?php

namespace Khill\Lavacharts\Support;

use Khill\Lavacharts\Exceptions\InvalidArgumentException;
use Khill\Lavacharts\Support\Contracts\Jsonable;

/**
 * Class StringValue
 *
 * Creates a new String value object while checking if it actually a string.
 *
 * @package   Khill\Lavacharts\Values
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright 2020 Kevin Hill
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class StringValue implements Jsonable
{
    /**
     * @var string
     */
    protected $value;

    /**
     * Create a new instance of a StringValue.
     *
     * @param  string $value
     * @return StringValue
     */
    public static function create($value)
    {
        return new static($value);
    }

    /**
     * Creates a new instance while checking if valid and returns the value.
     *
     * @param  string $value
     * @return string
     */
    public static function verify($value)
    {
        return self::create($value)->__toString();
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
        return (is_string($value) === true && empty($value) === false);
    }

    /**
     * Check if a string ends with another string.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * StringValue constructor.
     *
     * @param  string $value
     * @throws InvalidArgumentException
     */
    public function __construct($value)
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException($value, 'string');
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

    /**
     * Returns a customize JSON representation of an object.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
