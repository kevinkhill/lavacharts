<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Options;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * ScatterChart Class
 *
 * A chart that lets you render each series as a different marker type from the following list:
 * line, area, bars, candlesticks and stepped area.
 *
 * To assign a default marker type for series, specify the seriesType property.
 * Use the series property to specify properties of each series individually.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class ScatterChart extends Chart
{
    /**
     * Common Methods
     */
    use \Khill\Lavacharts\Traits\AnnotationsTrait;
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\CrosshairTrait;
    use \Khill\Lavacharts\Traits\CurveTypeTrait;
    use \Khill\Lavacharts\Traits\DataOpacityTrait;
    use \Khill\Lavacharts\Traits\EnableInteractivityTrait;
    use \Khill\Lavacharts\Traits\ForceIFrameTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\OrientationTrait;
    use \Khill\Lavacharts\Traits\PointShapeTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\ReverseCategoriesTrait;
    use \Khill\Lavacharts\Traits\SelectionModeTrait;
    use \Khill\Lavacharts\Traits\SeriesTrait;
    use \Khill\Lavacharts\Traits\ThemeTrait;
    use \Khill\Lavacharts\Traits\TrendlinesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'ScatterChart';

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
    const VIZ_CLASS = 'google.visualization.ScatterChart';

    /**
     * Default configuration options for the chart.
     *
     * @var array
     */
    private $scatterDefaults = [
        'annotations',
        'axisTitlesPosition',
        'crosshair',
        'curveType',
        'dataOpacity',
        'enableInteractivity',
        'forceIFrame',
        'hAxis',
        'lineWidth',
        'orientation',
        'pointShape',
        'pointSize',
        'reverseCategories',
        'selectionMode',
        'series',
        'theme',
        'trendlines',
        'vAxis'
    ];

    /**
     * Builds a new ScatterChart with the given label, datatable and options.
     *
     * @param  \Khill\Lavacharts\Values\Label         $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\DataTables\DataTable $datatable DataTable used for the chart.
     * @param array                                   $config
     */
    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->scatterDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }
}
