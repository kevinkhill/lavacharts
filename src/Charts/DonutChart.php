<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Utils;
use \Khill\Lavacharts\Values\Label;
use \Khill\Lavacharts\Datatables\Datatable;

/**
 * DonutChart Class
 *
 * Alias for a pie chart with pieHolethat is rendered within the browser using SVG or VML. Displays
 * tooltips when hovering over slices.
 *
 *
 * @package    Lavacharts
 * @subpackage Charts
 * @since      1.0.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class DonutChart extends PieChart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'DonutChart';

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
     * Builds a new chart with the given label.
     *
     * @param  \Khill\Lavacharts\Values\Label $chartLabel Identifying label for the chart.
     * @param  \Khill\Lavacharts\Datatables\Datatable $datatable Datatable used for the chart.
     * @return self
     */
    public function __construct(Label $chartLabel, Datatable $datatable)
    {
        parent::__construct($chartLabel, $datatable);

        $this->defaults = array_merge($this->defaults, [
            'pieHole'
        ]);

        $this->pieHole(0.5);
    }

    /**
     * If between 0 and 1, displays a donut chart. The hole with have a radius
     * equal to $pieHole times the radius of the chart.
     *
     * @param  integer|float  $pieHole Size of the pie hole.
     * @return DonutChart
     */
    public function pieHole($pieHole)
    {
        if (Utils::between(0.0, $pieHole, 1.0) === false) {
            throw $this->invalidConfigValue(
                __FUNCTION__,
                'float',
                'while, 0 < pieHole < 1 '
            );
        }

        return $this->addOption([__FUNCTION__ => $pieHole]);
    }
}
