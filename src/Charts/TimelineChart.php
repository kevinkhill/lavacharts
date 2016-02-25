<?php

namespace Khill\Lavacharts\Charts;

/**
 * TimelineChart Class
 *
 *
 * A timeline is a chart that depicts how a set of resources are used over time.
 * If you're managing a software project and want to illustrate who is doing what
 * and when, or if you're organizing a conference and need to schedule meeting
 * rooms, a timeline is often a reasonable visualization choice.
 *
 *
 * @package    Khill\Lavacharts
 * @subpackage Charts
 * @since      3.1.0
 * @author     Kevin Hill <kevinkhill@gmail.com>
 * @copyright  (c) 2016, KHill Designs
 * @link       http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link       http://lavacharts.com                   Official Docs Site
 * @license    http://opensource.org/licenses/MIT MIT
 */
class TimelineChart extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'TimelineChart';

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
    const VIZ_PACKAGE = 'timeline';

    /**
     * Google's visualization class name.
     *
     * @var string
     */
    const VIZ_CLASS = 'google.visualization.Timeline';
}
