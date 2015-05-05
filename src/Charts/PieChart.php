<?php namespace Khill\Lavacharts\Charts;

/**
 * PieChart Class
 *
 * A pie chart that is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      v1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Configs\Slice;
use \Khill\Lavacharts\Configs\TextStyle;

class PieChart extends Chart
{
    public $type = 'PieChart';

    private $extraOptions = [
        'is3D',
        'slices',
        'pieSliceBorderColor',
        'pieSliceText',
        'pieSliceTextStyle',
        'pieStartAngle',
        'reverseCategories',
        'sliceVisibilityThreshold',
        'pieResidueSliceColor',
        'pieResidueSliceLabel'
    ];

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel, $this->extraOptions);
    }

    /**
     * If set to true, displays a three-dimensional chart.
     *
     * @param  bool               $is3D
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function is3D($is3D)
    {
        if (is_bool($is3D) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $is3D]);
    }

    /**
     * An array of slice objects, each describing the format of the
     * corresponding slice in the pie. To use default values for a slice,
     * specify a null. If a slice or a value is not specified, the global
     * value will be used.
     *
     * The values of the array keys will correspond to each numbered piece
     * of the pie, starting from 0. You can skip slices by assigning the
     * keys of the array as (int)s.
     *
     * This would apply slice values to the first and fourth slice of the pie
     * Example: array(
     *              0 => new Slice(),
     *              3 => new Slice()
     *          );
     *
     *
     * @param  array              $slices Array of slice objects
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function slices($slices)
    {
        if (is_array($slices) === false || empty($slices) === true) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'as (int) => (Slice)'
            );
        }

        $pie = [];

        foreach ($slices as $key => $slice) {
            $pie[$key] = $this->addSlice($slice);
        }

        return $this->addOption([__FUNCTION__ => $pie]);
    }

    /**
     * Supplemental function to add slices
     *
     * @param  Slice $slice
     * @return array
     */
    private function addSlice(Slice $slice)
    {
        return $slice->getValues();
    }

    /**
     * The color of the slice borders. Only applicable when the chart is
     * two-dimensional; is3D == false || null
     *
     * @param  string             $pieSliceBorderColor Valid HTML color
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function pieSliceBorderColor($pieSliceBorderColor)
    {
        if (Utils::nonEmptyString($pieSliceBorderColor) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->addOption([__FUNCTION__ => $pieSliceBorderColor]);
    }

    /**
     * The content of the text displayed on the slice. Can be one of the following:
     *
     * 'percentage' - The percentage of the slice size out of the total.
     * 'value' - The quantitative value of the slice.
     * 'label' - The name of the slice.
     * 'none' - No text is displayed.
     *
     * @param  string             $pieSliceText
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function pieSliceText($pieSliceText)
    {
        $values = array(
            'percentage',
            'value',
            'label',
            'none'
        );

        if (in_array($pieSliceText, $values, true) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Utils::arrayToPipedString($values)
            );
        }

        return $this->addOption([__FUNCTION__ => $pieSliceText]);
    }

    /**
     * An object that specifies the slice text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @param  TextStyle          $textStyle
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function pieSliceTextStyle(TextStyle $textStyle)
    {
        return $this->addOption($textStyle->toArray(__FUNCTION__));
    }

    /**
     * The angle, in degrees, to rotate the chart by. The default of 0 will
     * orient the leftmost edge of the first slice directly up.
     *
     * @param  int                $pieStartAngle Starting angle
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function pieStartAngle($pieStartAngle)
    {
        if (is_int($pieStartAngle) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this->addOption([__FUNCTION__ => $pieStartAngle]);
    }

    /**
     * If set to true, will draw slices counterclockwise. The default is to
     * draw clockwise.
     *
     * @param  bool               $reverseCategories
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function reverseCategories($reverseCategories)
    {
        if (is_bool($reverseCategories) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'bool'
            );
        }

        return $this->addOption([__FUNCTION__ => $reverseCategories]);
    }

    /**
     * The slice relative part, below which a slice will not show individually.
     * All slices that have not passed this threshold will be combined to a
     * single slice, whose size is the sum of all their sizes. Default is not
     * to show individually any slice which is smaller than half a degree.
     *
     * @param  int|float          $sliceVizThreshold
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function sliceVisibilityThreshold($sliceVizThreshold)
    {
        if (is_numeric($sliceVizThreshold) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this->addOption([__FUNCTION__ => $sliceVizThreshold]);
    }

    /**
     * Color for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param  string             $pieResidueSliceColor
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function pieResidueSliceColor($pieResidueSliceColor)
    {
        if (Utils::nonEmptyString($pieResidueSliceColor) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'representing a valid HTML color'
            );
        }

        return $this->addOption([__FUNCTION__ => $pieResidueSliceColor]);
    }

    /**
     * A label for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param  string             $pieResidueSliceLabel
     * @throws InvalidConfigValue
     * @return PieChart
     */
    public function pieResidueSliceLabel($pieResidueSliceLabel)
    {
        if (Utils::nonEmptyString($pieResidueSliceLabel) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this->addOption([__FUNCTION__ => $pieResidueSliceLabel]);
    }
}
