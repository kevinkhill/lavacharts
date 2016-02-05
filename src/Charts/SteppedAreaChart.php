<?php

namespace Khill\Lavacharts\Charts;

/**
 * SteppedAreaChart Class
 *
 * A stacking, stair like version of the AreaChart.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class SteppedAreaChart extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'SteppedAreaChart';

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
    const VIZ_CLASS = 'google.visualization.SteppedAreaChart';
}
