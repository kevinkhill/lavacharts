<?php

namespace Khill\Lavacharts\Charts;

/**
 * GaugeChart Class
 *
 * A gauge with a dial, rendered within the browser using SVG or VML.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      2.2.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2015, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class GaugeChart extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'GaugeChart';

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
    const VIZ_PACKAGE = 'gauge';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Gauge';
}
