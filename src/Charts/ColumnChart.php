<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * ColumnChart Class
 *
 * A vertical bar chart that is rendered within the browser using SVG or VML.
 * Displays tips when hovering over bars. For a horizontal version of this
 * chart, see the Bar Chart.
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
class ColumnChart extends Chart
{
    /**
     * Common methods
     */
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\BarGroupWidthTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\IsStackedTrait;
    use \Khill\Lavacharts\Traits\TrendlinesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'ColumnChart';

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
    const VIZ_CLASS = 'google.visualization.ColumnChart';

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    private $columnDefaults = [
        'axisTitlesPosition',
        'barGroupWidth',
        'focusTarget',
        'hAxis',
        'isHtml',
        'isStacked',
        'trendlines',
        'vAxes',
        'vAxis'
    ];

    /**
     * Builds a new ColumnChart with the given label, datatable and options.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                   $config
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->columnDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }
}
