<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Google;

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
     * @inheritdoc
     */
    public function getJsClass()
    {
        return Google::visualization('PieChart');
    }
}
