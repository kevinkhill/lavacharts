<?php namespace Khill\Lavacharts\Charts;

/**
 * PieChart Class
 *
 * A pie chart that is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @category  Class
 * @package   Khill\Lavacharts\Charts
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2014, KHill Designs
 * @link      https://github.com/kevinkhill/LavaCharts GitHub Repository Page
 * @link      http://kevinkhill.github.io/LavaCharts/ GitHub Project Page
 * @license   http://www.gnu.org/licenses/MIT MIT
 */

use Khill\Lavacharts\Helpers\Helpers;
use Khill\Lavacharts\Configs\Slice;
use Khill\Lavacharts\Configs\TextStyle;

class PieChart extends Chart
{
    public $type = 'PieChart';

    public function __construct($chartLabel)
    {
        parent::__construct($chartLabel);

        $this->defaults = array_merge(
            $this->defaults,
            array(
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
            )
        );
    }

    /**
     * If set to true, displays a three-dimensional chart.
     *
     * @param  boolean                                        $is3D
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function is3D($is3D)
    {
        if (is_bool($is3D)) {
            $this->addOption(array('is3D' => $is3D));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this;
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
     * @param array Array of slice objects
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function slices($slices)
    {
        if (is_array($slices) && ! empty($slices)) {
            $pie = array();

            foreach ($slices as $key => $slice) {
                $pie[$key] = $this->addSlice($slice);
            }

            $this->addOption(array('slices' => $pie));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'array',
                'as (int) => (Slice)'
            );
        }

        return $this;
    }

    /**
     * Supplemental function to add slices
     */
    private function addSlice(Slice $slice)
    {
        return $slice->getValues();
    }

    /**
     * The color of the slice borders. Only applicable when the chart is
     * two-dimensional; is3D == false || null
     *
     * @param string HTML color
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function pieSliceBorderColor($pieSliceBorderColor)
    {
        if (is_string($pieSliceBorderColor)) {
            $this->addOption(array('pieSliceBorderColor' => $pieSliceBorderColor));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
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
     * @param  string                                         $pieSliceText
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function pieSliceText($pieSliceText)
    {
        $values = array(
            'percentage',
            'value',
            'label',
            'none'
        );

        if (is_string($pieSliceText) && in_array($pieSliceText, $values)) {
            $this->addOption(array('pieSliceText' => $pieSliceText));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'with a value of '.Helpers::arrayToPipedString($values)
            );
        }

        return $this;
    }

    /**
     * An object that specifies the slice text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @param  Khill\Lavacharts\Configs\TextStyle             $textStyle
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function pieSliceTextStyle(TextStyle $textStyle)
    {
        $this->addOption(array('pieSliceTextStyle' => $textStyle->getValues()));

        return $this;
    }

    /**
     * The angle, in degrees, to rotate the chart by. The default of 0 will
     * orient the leftmost edge of the first slice directly up.
     *
     * @param int start angle
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function pieStartAngle($pieStartAngle)
    {
        if (is_int($pieStartAngle)) {
            $this->addOption(array('pieStartAngle' => $pieStartAngle));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'int'
            );
        }

        return $this;
    }

    /**
     * If set to true, will draw slices counterclockwise. The default is to
     * draw clockwise.
     *
     * @param  boolean                                        $reverseCategories
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function reverseCategories($reverseCategories)
    {
        if (is_bool($reverseCategories)) {
            $this->addOption(array('reverseCategories' => $reverseCategories));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'boolean'
            );
        }

        return $this;
    }

    /**
     * The slice relative part, below which a slice will not show individually.
     * All slices that have not passed this threshold will be combined to a
     * single slice, whose size is the sum of all their sizes. Default is not
     * to show individually any slice which is smaller than half a degree.
     *
     * @param  numeric                                        $sliceVisibilityThreshold
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function sliceVisibilityThreshold($sliceVizThreshold)
    {
        if (is_numeric($sliceVizThreshold)) {
            $this->addOption(array('sliceVisibilityThreshold' => $sliceVizThreshold));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'numeric'
            );
        }

        return $this;
    }

    /**
     * Color for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param  type                                           $pieResidueSliceColor
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function pieResidueSliceColor($pieResidueSliceColor)
    {
        if (is_string($pieResidueSliceColor)) {
            $this->addOption(array('pieResidueSliceColor' => $pieResidueSliceColor));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string',
                'representing a valid HTML color'
            );
        }

        return $this;
    }

    /**
     * A label for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param  string                                         $pieResidueSliceLabel
     * @throws Khill\Lavacharts\Exceptions\InvalidConfigValue
     * @return Khill\Lavacharts\Charts\PieChart
     */
    public function pieResidueSliceLabel($pieResidueSliceLabel)
    {
        if (is_string($pieResidueSliceLabel)) {
            $this->addOption(array('pieResidueSliceLabel' => $pieResidueSliceLabel));
        } else {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'string'
            );
        }

        return $this;
    }
}
