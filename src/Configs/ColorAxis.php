<?php

namespace Khill\Lavacharts\Configs;

use \Khill\Lavacharts\JsonConfig;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * ColorAxis Object
 *
 * An object that specifies a mapping between color column values and colors
 * or a gradient scale.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Configs
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ColorAxis extends JsonConfig
{
    /**
     * Type of JsonConfig object
     *
     * @var string
     */
    const TYPE = 'ColorAxis';

    /**
     * Default options for TextStyles
     *
     * @var array
     */
    private $defaults = [
        'minValue',
        'maxValue',
        'values',
        'colors'
    ];

    /**
     * Builds the ColorAxis object with specified options
     *
     * @param  array                 $config
     * @return \Khill\Lavacharts\Configs\ColorAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigProperty
     */
    public function __construct($config = [])
    {
        $options = new Options($this->defaults);

        parent::__construct($options, $config);
    }

    /**
     * If present, specifies a minimum value for chart color data. Color data
     * values of this value and lower will be rendered as the first color in
     * the $this->colors range.
     *
     * @param  int|float $minValue
     * @return \Khill\Lavacharts\Configs\ColorAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function minValue($minValue)
    {
        return $this->setNumericOption(__FUNCTION__, $minValue);
    }

    /**
     * If present, specifies a maximum value for chart color data. Color data
     * values of this value and higher will be rendered as the last color in
     * the $this->colors range.
     *
     * @param  int|float $maxValue
     * @return \Khill\Lavacharts\Configs\ColorAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function maxValue($maxValue)
    {
        return $this->setNumericOption(__FUNCTION__, $maxValue);
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
     * @param  array $values
     * @return \Khill\Lavacharts\Configs\ColorAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function values($values)
    {
        if (is_array($values) === false || Utils::arrayValuesCheck($values, 'numeric') === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array',
                'with values as [ int | float ]'
            );
        }

        return $this->setOption(__FUNCTION__, $values);
    }

    /**
     * Colors to assign to values in the visualization. An array of strings,
     * where each element is an HTML color string.
     *
     * For example: ['red', '#004411']
     * You must have at least two values; the gradient will include all your
     * values, plus calculated intermediary values, with the first color as the
     * smallest value, and the last color as the highest.
     *
     * @param  array $colors
     * @return \Khill\Lavacharts\Configs\ColorAxis
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function colors($colors)
    {
        if (is_array($colors) === false || count($colors) <= 1 || Utils::arrayValuesCheck($colors, 'string') === false) {
            throw new InvalidConfigValue(
                __FUNCTION__,
                'array',
                'with a minimum of two strings representing html colors.'
            );
        }

        return $this->setOption(__FUNCTION__, $colors);
    }
}
