<?php namespace Khill\Lavacharts\Charts;
/**
 * PieChart Class
 *
 * A pie chart that is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @author Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2013, KHill Designs
 * @link https://github.com/kevinkhill/Codeigniter-gCharts GitHub Repository Page
 * @link http://kevinkhill.github.io/Codeigniter-gCharts/ GitHub Project Page
 * @license http://opensource.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Charts\Chart;

class PieChart extends Chart
{
    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge($this->defaults, array(
            'is3D',
            'slices',
            'pieSliceBorderColor',
            'pieSliceText',
            'pieSliceTextStyle',
            'pieStartAngle',
            'reverseCategories',
            'sliceVisibilityThreshold',
            'pieResidueSliceColor',
            'pieResidueSliceLabel',
        ));
    }

    /**
     * If set to true, displays a three-dimensional chart.
     *
     * @param boolean $is3D
     * @return \PieChart
     */
    public function is3D($is3D)
    {
        if(is_bool($is3D))
        {
            $this->addOption(array('is3D' => $is3D));
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * An array of slice objects, each describing the format of the
     * corresponding slice in the pie. To use default values for a slice,
     * specify a NULL. If a slice or a value is not specified, the global
     * value will be used.
     *
     * The values of the array keys will correspond to each numbered piece
     * of the pie, starting from 0. You can skip slices by assigning the
     * keys of the array as (int)s.
     *
     * This would apply slice values to the first and fourth slice of the pie
     * Example: array(
     *              0 => new slice(),
     *              3 => new slice()
     *          );
     *
     *
     * @param array Array of slice objects
     * @return \PieChart
     */
    public function slices($slices)
    {
        if(is_array($slices) && array_values_check($slices, 'class', 'slice'))
        {
            $pizzaBox = array();

            foreach($slices as $key => $slice)
            {
                $pizzaBox[$key] = $slice->values();
            }

            $this->addOption(array('slices' => $pizzaBox));
        } else {
            $this->type_error(__FUNCTION__, 'array', 'with keys as (int) and values as (slice)');
        }

        return $this;
    }

    /**
     * The color of the slice borders. Only applicable when the chart is
     * two-dimensional; is3D == FALSE || NULL
     *
     * @param string HTML color
     * @return \PieChart
     */
    public function pieSliceBorderColor($pieSliceBorderColor)
    {
        if(is_string($pieSliceBorderColor))
        {
            $this->addOption(array('pieSliceBorderColor' => $pieSliceBorderColor));
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

    /**
     * The content of the text displayed on the slice. Can be one of the following:
     *
     * 'percentage' - The percentage of the slice size out of the total.
     * 'value' - The quantitative value of the slice.
     * 'label' - The name of the slice.
     * 'none' - No text is displayed.
     *
     * @param string $pieSliceText
     * @return \PieChart
     */
    public function pieSliceText($pieSliceText)
    {
        $values = array(
            'percentage',
            'value',
            'label',
            'none'
        );

        if(in_array($pieSliceText, $values))
        {
            $this->addOption(array('pieSliceText' => $pieSliceText));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'with a value of '.Helpers::array_string($values));
        }

        return $this;
    }

    /**
     * An object that specifies the slice text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @param textStyle $textStyle
     * @return \PieChart
     */
    public function pieSliceTextStyle($textStyle)
    {
        if(is_a($textStyle, 'textStyle'))
        {
            //$this->addOption($textStyle->toArray(__FUNCTION__));
            $this->addOption(array('pieSliceTextStyle' => $textStyle->values()));
        } else {
            $this->type_error(__FUNCTION__, 'textStyle');
        }

        return $this;
    }

    /**
     * The angle, in degrees, to rotate the chart by. The default of 0 will
     * orient the leftmost edge of the first slice directly up.
     *
     * @param int start angle
     * @return \PieChart
     */
    public function pieStartAngle($pieStartAngle)
    {
        if(is_int($pieStartAngle))
        {
            $this->addOption(array('pieStartAngle' => $pieStartAngle));
        } else {
            $this->type_error(__FUNCTION__, 'int');
        }

        return $this;
    }

    /**
     * If set to true, will draw slices counterclockwise. The default is to
     * draw clockwise.
     *
     * @param boolean $reverseCategories
     * @return \PieChart
     */
    public function reverseCategories($reverseCategories)
    {
        if(is_bool($reverseCategories))
        {
            $this->addOption(array('reverseCategories' => $reverseCategories));
        } else {
            $this->type_error(__FUNCTION__, 'boolean');
        }

        return $this;
    }

    /**
     * The slice relative part, below which a slice will not show individually.
     * All slices that have not passed this threshold will be combined to a
     * single slice, whose size is the sum of all their sizes. Default is not
     * to show individually any slice which is smaller than half a degree.
     *
     * @param numeric $sliceVisibilityThreshold
     * @return \PieChart
     */
    public function sliceVisibilityThreshold($sliceVisibilityThreshold)
    {
        if(is_numeric($sliceVisibilityThreshold))
        {
            $this->addOption(array('sliceVisibilityThreshold' => $sliceVisibilityThreshold));
        } else {
            $this->type_error(__FUNCTION__, 'numeric');
        }

        return $this;
    }

    /**
     * Color for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param type $pieResidueSliceColor
     * @return \PieChart
     */
    public function pieResidueSliceColor($pieResidueSliceColor)
    {
        if(is_string($pieResidueSliceColor))
        {
            $this->addOption(array('pieResidueSliceColor' => $pieResidueSliceColor));
        } else {
            $this->type_error(__FUNCTION__, 'string', 'representing a valide HTML color');
        }

        return $this;
    }

    /**
     * A label for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param string $pieResidueSliceLabel
     * @return \PieChart
     */
    public function pieResidueSliceLabel($pieResidueSliceLabel)
    {
        if(is_string($pieResidueSliceLabel))
        {
            $this->addOption(array('pieResidueSliceLabel' => $pieResidueSliceLabel));
        } else {
            $this->type_error(__FUNCTION__, 'string');
        }

        return $this;
    }

}
