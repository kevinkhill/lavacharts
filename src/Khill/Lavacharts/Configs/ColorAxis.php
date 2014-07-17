<?php namespace Khill\Lavacharts\Configs;

/**
 * ColorAxis Object
 *
 * An object that specifies a mapping between color column values and colors
 * or a gradient scale.
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

class ColorAxis extends ConfigOptions
{
    /**
     * @var int Minimum value for chart color data.
     */
    public $minValue = null;

    /**
     * @var int Maximum value for chart color data.
     */
    public $maxValue = null;

    /**
     * @var array Controls how values are associated with colors.
     */
    public $values = null;

    /**
     * @var array Colors to assign to values in the visualization.
     */
    public $colors = null;


    /**
     * Builds the colorAxis object with specified options
     *
     * @param  array config
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigProperty
     * @return Khill\Lavacharts\Configs\ColorAxis
     */
    public function __construct($config = array())
    {
        $this->className = str_replace("Khill\\Lavacharts\\Configs\\", '', __CLASS__);
        $this->options = array(
            'minValue',
            'maxValue',
            'values',
            'colors'
        );

        parent::__construct($config);
    }

    /**
     * If present, specifies a minimum value for chart color data. Color data
     * values of this value and lower will be rendered as the first color in
     * the $this->colors range.
     *
     * @param numeric minValue
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ColorAxis
     */
    public function minValue($minValue)
    {
        if (is_numeric($minValue)) {
            $this->minValue = $minValue;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * If present, specifies a maximum value for chart color data. Color data
     * values of this value and higher will be rendered as the last color in
     * the $this->colors range.
     *
     * @param numeric maxValue
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ColorAxis
     */
    public function maxValue($maxValue)
    {
        if (is_numeric($maxValue)) {
            $this->maxValue = $maxValue;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * If present, controls how values are associated with colors. Each value
     * is associated with the corresponding color in the $this->colors array.
     * These values apply to the chart color data.
     *
     * Coloring is done according to a gradient of the values specified here.
     * Not specifying a value for this option is equivalent to specifying
     * [minValue, maxValue].
     *
     * @param array values
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ColorAxis
     */
    public function values($values)
    {
        if (is_array($values) && Helpers::arrayValuesCheck($values, 'numeric')) {
            $this->values = $values;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array',
                'with values as [ int | float ]'
            );
        }
        return $this;
    }

    /**
     * Colors to assign to values in the visualization. An array of strings,
     * where each element is an HTML color string.
     *
     * For example: $this->colors = array('red', '#004411')
     * You must have at least two values; the gradient will include all your
     * values, plus calculated intermediary values, with the first color as the
     * smallest value, and the last color as the highest.
     *
     * @param array colors
     *
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Configs\ColorAxis
     */
    public function colors($colors)
    {
        if (is_array($colors) && Helpers::arrayValuesCheck($colors, 'string')) {
            $this->colors = $colors;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array',
                'with values as strings'
            );
        }
        return $this;
    }
}
