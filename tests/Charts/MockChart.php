<?php

namespace Khill\Lavacharts\Tests\Charts;

use \Khill\Lavacharts\Charts\Chart;
use \Khill\Lavacharts\Configs\Options;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\DataTables\DataTable;

/**
 * MockChart Class
 *
 * This is used to apply all the traits for testing, as well as testing the parent methods for all the charts.
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
class MockChart extends Chart
{
    use \Khill\Lavacharts\Traits\AnnotationsTrait;
    use \Khill\Lavacharts\Traits\AreaOpacityTrait;
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\BarGroupWidthTrait;
    use \Khill\Lavacharts\Traits\ColorAxisTrait;
    use \Khill\Lavacharts\Traits\CrosshairTrait;
    use \Khill\Lavacharts\Traits\CurveTypeTrait;
    use \Khill\Lavacharts\Traits\DataOpacityTrait;
    use \Khill\Lavacharts\Traits\EnableInteractivityTrait;
    use \Khill\Lavacharts\Traits\FocusTargetTrait;
    use \Khill\Lavacharts\Traits\ForceIFrameTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxesTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\InterpolateNullsTrait;
    use \Khill\Lavacharts\Traits\IsStackedTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\OrientationTrait;
    use \Khill\Lavacharts\Traits\PointShapeTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\ReverseCategoriesTrait;
    use \Khill\Lavacharts\Traits\SelectionModeTrait;
    use \Khill\Lavacharts\Traits\SeriesTrait;
    use \Khill\Lavacharts\Traits\ThemeTrait;
    use \Khill\Lavacharts\Traits\VerticalAxesTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    const TYPE = 'MockChart';

    const VERSION = '1';

    const VIZ_PACKAGE = 'mockchart';

    const VIZ_CLASS = 'google.visualization.MockChart';

    private $mockDefaults = [
        'annotations',
        'areaOpacity',
        'axisTitlesPosition',
        'barGroupWidth',
        'colorAxis',
        'crosshair',
        'curveType',
        'dataOpacity',
        'enableInteractivity',
        'focusTarget',
        'forceIFrame',
        'hAxes',
        'hAxis',
        'interpolateNulls',
        'isStacked',
        'lineWidth',
        'orientation',
        'pointShape',
        'pointSize',
        'reverseCategories',
        'selectionMode',
        'series',
        'theme',
        'vAxes',
        'vAxis'
    ];

    public function __construct(Label $chartLabel, DataTable $datatable, $config = [])
    {
        $options = new Options($this->mockDefaults);

        parent::__construct($chartLabel, $datatable, $options, $config);
    }
}
