<?php

namespace Khill\Lavacharts\Charts;

use const Khill\Lavacharts\Support\GOOGLE_VISUALIZATION;

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
 * @package   Khill\Lavacharts\Charts
 * @since     3.0.5
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class TimelineChart extends Chart
{
    /**
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return 'timeline';
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return GOOGLE_VISUALIZATION . 'Timeline';
    }
}
