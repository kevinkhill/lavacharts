<?php

namespace Khill\Lavacharts\Dashboards\Wrappers;

use Khill\Lavacharts\Charts\Chart;
use Khill\Lavacharts\Support\Contracts\JsClass;

/**
 * ChartWrapper Class
 *
 * Used for wrapping charts to use in dashboards.
 *
 * @package   Khill\Lavacharts\Dashboards\Wrappers
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ChartWrapper extends Wrapper
{
    /**
     * Builds a ChartWrapper object.
     *
     * @param Chart  $chart
     * @param string $containerId
     */
    public function __construct(Chart $chart, $containerId)
    {
        $chart->setRenderable(false);

        parent::__construct($chart, $containerId);
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return self::GOOGLE_VISUALIZATION . 'ChartWrapper';
    }
}
