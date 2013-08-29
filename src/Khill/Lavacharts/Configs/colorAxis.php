<?php namespace Khill\Lavacharts\Configs;
/**
 * colorAxis Object
 *
 * An object that specifies a mapping between color column values and colors
 * or a gradient scale.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

class colorAxis extends configOptions
{
    /**
     * Minimum value for chart color data.
     *
     * @var int
     */
    public $minValue = NULL;

    /**
     * Maximum value for chart color data.
     *
     * @var int
     */
    public $maxValue = NULL;

    /**
     * Controls how values are associated with colors.
     *
     * @var array
     */
    public $values = NULL;

    /**
     * Colors to assign to values in the visualization.
     *
     * @var array
     */
    public $colors = NULL;


    /**
     * Builds the colorAxis object with specified options
     *
     * @param array config
     * @return \colorAxis
     */
    public function __construct($config = array()) {

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
     * @return \colorAxis
     */
    public function minValue($minValue)
    {
        if(is_numeric($minValue))
        {
            $this->minValue = $minValue;
        } else {
            $this->type_error(__FUNCTION__, 'numeric');
        }

        return $this;
    }

    /**
     * If present, specifies a maximum value for chart color data. Color data
     * values of this value and higher will be rendered as the last color in
     * the $this->colors range.
     *
     * @param numeric maxValue
     * @return \colorAxis
     */
    public function maxValue($maxValue)
    {
        if(is_numeric($maxValue))
        {
            $this->maxValue = $maxValue;
        } else {
            $this->type_error(__FUNCTION__, 'numeric');
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
     * @return \colorAxis
     */
    public function values($values)
    {
        if(is_array($values) && Helpers::array_values_check($values, 'numeric'))
        {
            $this->values = $values;
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with values as [ int | float ]');
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
     * @return \colorAxis
     */
    public function colors($colors)
    {
        if(is_array($colors) && Helpers::array_values_check($colors, 'string'))
        {
            $this->colors = $colors;
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with values as strings');
        }
        return $this;
    }

}
