<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * LineChart Class
 *
 * A line chart that is rendered within the browser using SVG or VML. Displays
 * tips when hovering over points.
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
class LineChart extends Chart
{
    /**
     * Common Methods
     */
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\CurveTypeTrait;
    use \Khill\Lavacharts\Traits\FocusTargetTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\InterpolateNullsTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\TrendlinesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'LineChart';

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
    const VIZ_CLASS = 'google.visualization.LineChart';

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    private $lineDefaults = [
        'axisTitlesPosition',
        'curveType',
        'focusTarget',
        'hAxis',
        'interpolateNulls',
        'lineWidth',
        'pointSize',
        'trendlines',
        'vAxes',
        'vAxis'
    ];

    /**
     * Builds a new LineChart with the given label, datatable and options.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                   $config
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->lineDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }
}
