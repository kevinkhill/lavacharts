<?php

namespace Khill\Lavacharts\DataTables\Formats;

use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Support\Traits\OptionsTrait as HasOptions;

/**
 * Class Format
 *
 * The base class for the individual configuration objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Khill\Lavacharts\DataTables\Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class Format implements \JsonSerializable
{
    use HasOptions;

    /**
     * Builds the Options object.
     * Passing an array of key value pairs will set the configuration for each
     * child object created from this parent object.
     *
     * @param \Khill\Lavacharts\DataTables\Formats\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Custom string representation of the Formatter.
     *
     * @return string The javascript visualization class as a string.
     */
    public function __toString()
    {
        return 'google.visualization.' . static::TYPE;
    }

    /**
     * Returns the format type.
     *
     * @since 3.0.0
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Custom serialization of the Format
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->options;
    }
}
