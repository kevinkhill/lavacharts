<?php

namespace Khill\Lavacharts\Charts;

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
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'GanttChart';

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
    const VISUALIZATION_PACKAGE = 'gantt';

    /**
     * Returns the google javascript package name.
     *
     * @since  3.0.5
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.Gantt';
    }
}
