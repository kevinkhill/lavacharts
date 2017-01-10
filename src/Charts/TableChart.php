<?php

namespace Khill\Lavacharts\Charts;

/**
 * Table Chart Class
 *
 * A table chart is rendered within the browser. Displays a data from a DataTable in an easily sortable form.
 * Can be searched by rendering as a wrapper and binding to a control within a dashboard.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     3.0.0
 * @author    Peter Draznik <peter.draznik@38thStreetStudios.com>
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, 38th Street Studios
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class TableChart extends Chart
{
    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'TableChart';

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
    const VISUALIZATION_PACKAGE = 'table';

    /**
     * Returns the google javascript package name.
     *
     * @since  3.0.5
     * @return string
     */
    public function getJsClass()
    {
        return 'google.visualization.Table';
    }
}
