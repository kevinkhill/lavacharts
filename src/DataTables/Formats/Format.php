<?php

namespace Khill\Lavacharts\DataTables\Formats;

use Khill\Lavacharts\Support\Contracts\VisualizationInterface;
use Khill\Lavacharts\Support\Customizable;
use Khill\Lavacharts\Support\Contracts\JsonableInterface;

/**
 * Class Format
 *
 * The base class for the individual format objects, providing common
 * functions to the child objects.
 *
 *
 * @package    Khill\Lavacharts\DataTables\Formats
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @since      3.0.0
 * @copyright  (c) 2017, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT      MIT
 */
class Format extends Customizable implements JsonableInterface
{
    /**
     * Static method for creating new format objects.
     *
     * @param string $type
     * @param array  $options
     * @return \Khill\Lavacharts\DataTables\Formats\Format
     */
    public static function create($type, array $options = [])
    {
        $format =  __NAMESPACE__ . '\\' . $type;

        return new $format($options);
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
     * JSON representation of the Format.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Javascript representation of the Format.
     *
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.' . static::TYPE;
    }
}
