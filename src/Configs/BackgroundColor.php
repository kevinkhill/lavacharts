<?php namespace Khill\Lavacharts\Configs;

/**
 * backgroundColor Object
 *
 * An object containing all the values for the backgroundColor object which can
 * be passed into the chart's options.
 *
 *
 * @package    Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class BackgroundColor extends ConfigObject
{
    /**
     * The color of the chart border, as an HTML color string.
     *
     * @var string Valid HTML color.
     */
    public $stroke;

    /**
     * The border width, in pixels.
     *
     * @var int Width in number of pixels.
     */
    public $strokeWidth;

    /**
     * The chart fill color, as an HTML color string.
     *
     * @var string Valid HTML color.
     */
    public $fill;


    /**
     * Builds the backgroundColor object with specified options
     *
     * Pass an associative array with values for the keys
     * [ stroke | strokeWidth | fill ]
     *
     * @param  array                 $config Configuration options
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return BackgroundColor
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * Sets the chart border color. Example: 'red' or '#A2A2A2'
     *
     * @param  string          $stroke Valid HTML color string.
     * @return BackgroundColor
     */
    public function stroke($stroke)
    {
        if (is_string($stroke)) {
            $this->stroke = $stroke;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }

    /**
     * Sets the chart border width.
     *
     * @param  int             $strokeWidth Border width, in pixels.
     * @return BackgroundColor
     */
    public function strokeWidth($strokeWidth)
    {
        if (is_int($strokeWidth)) {
            $this->strokeWidth = $strokeWidth;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * Sets the chart color fill, Example: 'blue' or '#C5C5C5'
     *
     * @param  string          $fill Valid HTML color string.
     * @return BackgroundColor
     */
    public function fill($fill)
    {
        if (is_string($fill)) {
            $this->fill = $fill;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }
}
