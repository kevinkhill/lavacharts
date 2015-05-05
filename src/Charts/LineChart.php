<?php namespace Khill\Lavacharts\Charts;

/**
 * LineChart Class
 *
 * A line chart that is rendered within the browser using SVG or VML. Displays
 * tips when hovering over points.
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

class LineChart extends Chart
{
    use \Khill\Lavacharts\Traits\AxisTitlesPositionTrait;
    use \Khill\Lavacharts\Traits\CurveTypeTrait;
    use \Khill\Lavacharts\Traits\FocusTargetTrait;
    use \Khill\Lavacharts\Traits\HorizontalAxisTrait;
    use \Khill\Lavacharts\Traits\InterpolateNullsTrait;
    use \Khill\Lavacharts\Traits\LineWidthTrait;
    use \Khill\Lavacharts\Traits\PointSizeTrait;
    use \Khill\Lavacharts\Traits\VerticalAxisTrait;

    /**
     * Builds a new chart with the given label.
     *
     * @param  string $chartLabel Identifying label for the chart.
     * @return Chart
     */
    public function __construct($chartLabel)
    {
        $this->label        = $chartLabel;
        $this->type         = (new \ReflectionClass($this))->getShortName();
        $this->version      = '1';
        $this->jsPackage    = 'corechart';
        $this->jsClass      = 'google.visualization.' . $this->type;
        $this->extraOptions = [
            'axisTitlesPosition',
            'curveType',
            'focusTarget',
            'hAxis',
            'interpolateNulls',
            'lineWidth',
            'pointSize',
            //'vAxes',
            'vAxis'
        ];

        parent::__construct();
    }
}
