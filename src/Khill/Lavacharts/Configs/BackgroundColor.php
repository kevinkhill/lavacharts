<?php namespace Khill\Lavacharts\Configs;

/**
 * backgroundColor Object
 *
 * An object containing all the values for the backgroundColor object which can
 * be passed into the chart's options.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Configs
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class BackgroundColor extends ConfigOptions
{
    /**
     * The color of the chart border, as an HTML color string.
     *
     * @var string Valid HTML color.
     */
    public $stroke = null;

    /**
     * The border width, in pixels.
     *
     * @var int Width in number of pixels.
     */
    public $strokeWidth = null;

    /**
     * The chart fill color, as an HTML color string.
     *
     * @var string Valid HTML color.
     */
    public $fill = null;


    /**
     * Builds the backgroundColor object with specified options
     *
     * Pass an associative array with values for the keys
     * [ stroke | strokeWidth | fill ]
     *
     * @param  array Configuration options
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return backgroundColor
     */
    public function __construct($config = array())
    {

        $this->options = array(
            'stroke',
            'strokeWidth',
            'fill'
        );

        parent::__construct($config);
    }

    /**
     * Sets the chart border color. Example: 'red' or '#A2A2A2'
     *
     * @param string Valid HTML color string.
     *
     * @return backgroundColor
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
     * @param int Border width, in pixels.
     *
     * @return backgroundColor
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
     * @param string Valid HTML color string.
     *
     * @return backgroundColor
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
