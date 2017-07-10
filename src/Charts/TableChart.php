<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Google;

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
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return 'table';
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return Google::visualization('Table');
    }
}
