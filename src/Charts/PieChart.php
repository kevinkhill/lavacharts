<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\DataTable;
use \Khill\Lavacharts\Configs\TextStyle;
use \Khill\Lavacharts\Exceptions\InvalidConfigValue;

/**
 * PieChart Class
 *
 * A pie chart that is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class PieChart extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'PieChart';

    /**
     * Javascript chart version.
     *
     * @var string
     */
    const VERSION = '1';

    /**
     * Javascript chart package.
     *
     * @var string
     */
    const VIZ_PACKAGE = 'corechart';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.PieChart';

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    private $pieDefaults = [
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

    /**
     * Builds a new PieChart with the given label, datatable and options.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                   $config
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->pieDefaults);

        if (isset($this->donutDefaults)) {
            $options->extend($this->donutDefaults);
            $options->set('pieHole', 0.5);
        }

        parent::__construct($chartLabel, $datatable, $options, $config);
    }

    /**
     * If set to true, displays a three-dimensional chart.
     *
     * @param  bool $is3D
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function is3D($is3D)
    {
        return $this->setBoolOption(__FUNCTION__, $is3D);
    }

    /**
     * An array of slice objects, each describing the format of the
     * corresponding slice in the pie.
     *
     * To use default values for a slice, specify a null. If a slice or a value is not specified,
     * the global value will be used.
     *
     * The values of the array keys will correspond to each numbered piece
     * of the pie, starting from 0. You can skip slices by assigning the
     * keys of the array as (int)s.
     *
     *
     * @param  array $slices Array of slice objects
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function slices($slices)
    {
        if (Utils::arrayIsMulti($slices) === false) {
            throw new InvalidConfigValue(
                static::TYPE . '->' . __FUNCTION__,
                'array',
                'as (int) => (Slice)'
            );
        }

        $pie = [];

        foreach ($slices as $index => $slice) {
            $pie[$index] = $slice;
        }

        return $this->setOption(__FUNCTION__, $pie);
    }

    /**
     * The color of the slice borders. Only applicable when the chart is
     * two-dimensional; is3D == false || null
     *
     * @param  string $pieSliceBorderColor Valid HTML color
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pieSliceBorderColor($pieSliceBorderColor)
    {
        return $this->setStringOption(__FUNCTION__, $pieSliceBorderColor);
    }

    /**
     * The content of the text displayed on the slice. Can be one of the following:
     *
     * 'percentage' - The percentage of the slice size out of the total.
     * 'value' - The quantitative value of the slice.
     * 'label' - The name of the slice.
     * 'none' - No text is displayed.
     *
     *
     * @param  string $pieSliceText
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pieSliceText($pieSliceText)
    {
        $values = [
            'percentage',
            'value',
            'label',
            'none'
        ];

        return $this->setStringInArrayOption(__FUNCTION__, $pieSliceText, $values);
    }

    /**
     * An object that specifies the slice text style. create a new textStyle()
     * object, set the values then pass it to this function or to the constructor.
     *
     * @param  array $textStyleConfig
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pieSliceTextStyle($textStyleConfig)
    {
        return $this->setOption(__FUNCTION__, new TextStyle($textStyleConfig));
    }

    /**
     * The angle, in degrees, to rotate the chart by. The default of 0 will
     * orient the leftmost edge of the first slice directly up.
     *
     * @param  int $pieStartAngle Starting angle
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pieStartAngle($pieStartAngle)
    {
        return $this->setIntOption(__FUNCTION__, $pieStartAngle);
    }

    /**
     * If set to true, will draw slices counterclockwise. The default is to
     * draw clockwise.
     *
     * @param  bool $reverseCategories
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function reverseCategories($reverseCategories)
    {
        return $this->setBoolOption(__FUNCTION__, $reverseCategories);
    }

    /**
     * The slice relative part, below which a slice will not show individually.
     * All slices that have not passed this threshold will be combined to a
     * single slice, whose size is the sum of all their sizes. Default is not
     * to show individually any slice which is smaller than half a degree.
     *
     * @param  integer|float $sliceVizThreshold
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function sliceVisibilityThreshold($sliceVizThreshold)
    {
        return $this->setNumericOption(__FUNCTION__, $sliceVizThreshold);
    }

    /**
     * Color for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param  string $pieResidueSliceColor
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pieResidueSliceColor($pieResidueSliceColor)
    {
        return $this->setStringOption(__FUNCTION__, $pieResidueSliceColor);
    }

    /**
     * A label for the combination slice that holds all slices below
     * sliceVisibilityThreshold.
     *
     * @param  string $pieResidueSliceLabel
     * @return \Khill\Lavacharts\Charts\PieChart
     * @throws \Khill\Lavacharts\Exceptions\InvalidConfigValue
     */
    public function pieResidueSliceLabel($pieResidueSliceLabel)
    {
        return $this->setStringOption(__FUNCTION__, $pieResidueSliceLabel);
    }
}
