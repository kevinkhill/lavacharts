<?php

namespace Khill\Lavacharts\Charts;

/**
 * DonutChart Class
 *
 * A pie chart, with a hole in the center, that is rendered within the browser using SVG or VML.
 * Displays tooltips when hovering over slices.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     3.1.5
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
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
     * Returns the chart visualization package.
     *
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.PieChart';
    }
}
