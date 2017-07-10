<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Google;

/**
 * GanttChart Class
 *
 * A Gantt chart is a type of chart that illustrates the breakdown of a
 * project into its component tasks. Google Gantt charts illustrate the
 * start, end, and duration of tasks within a project, as well as any
 * dependencies a task may have. Google Gantt charts are rendered in
 * the browser using SVG. Like all Google charts, Gantt charts display
 * tooltips when the user hovers over the data.
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
class GanttChart extends Chart
{
    /**
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return 'gantt';
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return Google::visualization('Gantt');
    }
}
