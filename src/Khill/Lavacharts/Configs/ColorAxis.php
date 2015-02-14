<?php namespace Khill\Lavacharts\Configs;

/**
 * ColorAxis Object
 *
 * An object that specifies a mapping between color column values and colors
 * or a gradient scale.
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

use Khill\Lavacharts\Utils;
use Khill\Lavacharts\Exceptions\InvalidConfigValue;

class ColorAxis extends ConfigObject
{
    /**
     * Minimum value for chart color data.
     *
     * @var int
     */
    public $minValue;

    /**
     * Maximum value for chart color data.
     *
     * @var int
     */
    public $maxValue;

    /**
     * Controls how values are associated with colors.
     *
     * @var array
     */
    public $values;

    /**
     * Colors to assign to values in the visualization.
     *
     * @var array
     */
    public $colors;

    /**
     * Builds the ColorAxis object with specified options
     *
     * @param  array                 $config
     * @throws InvalidConfigValue
     * @throws InvalidConfigProperty
     * @return ColorAxis
     */
    public function __construct($config = array())
    {
        parent::__construct($this, $config);
    }

    /**
     * If present, specifies a minimum value for chart color data. Color data
     * values of this value and lower will be rendered as the first color in
     * the $this->colors range.
     *
     * @param  numeric            $minValue
     * @throws InvalidConfigValue
     * @return ColorAxis
     */
    public function minValue($minValue)
    {
        if (is_numeric($minValue)) {
            $this->minValue = (int) $minValue;
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
     * @param  numeric            $maxValue
     * @throws InvalidConfigValue
     * @return ColorAxis
     */
    public function maxValue($maxValue)
    {
        if (is_numeric($maxValue)) {
            $this->maxValue = (int) $maxValue;
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
     * @param  array              $values
     * @throws InvalidConfigValue
     * @return ColorAxis
     */
    public function values($values)
    {
        if (is_array($values) && Utils::arrayValuesCheck($values, 'numeric')) {
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
     * @param  array              $colors
     * @throws InvalidConfigValue
     * @return ColorAxis
     */
    public function colors($colors)
    {
        if (is_array($colors) && Utils::arrayValuesCheck($colors, 'string') && count($colors) > 1) {
            $this->colors = $colors;
        } else {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array',
                'minimum of two, with values as html color strings'
            );
        }

        return $this;
    }
}
