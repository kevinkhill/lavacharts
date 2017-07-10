<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Google;

/**
 * GaugeChart Class
 *
 * A gauge with a dial, rendered within the browser using SVG or VML.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     2.2.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class GaugeChart extends Chart
{
    /**
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return 'gauge';
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return Google::visualization('Gauge');
    }
}
