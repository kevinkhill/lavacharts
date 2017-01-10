<?php

namespace Khill\Lavacharts\Charts;

use \Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;
use \Khill\Lavacharts\Support\Traits\MaterialRenderableTrait as MaterialRenderable;

/**
 * ScatterChart Class
 *
 * A chart that lets you render each series as a different marker type from the following list:
 * line, area, bars, candlesticks and stepped area.
 *
 * To assign a default marker type for series, specify the seriesType property.
 * Use the series property to specify properties of each series individually.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     3.0.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class ScatterChart extends Chart
{
    use PngRenderable, MaterialRenderable;

    /**
     * Javascript chart type.
     *
     * @var string
     */
    const TYPE = 'ScatterChart';

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
    const VISUALIZATION_PACKAGE = 'corechart';

    /**
     * Returns the chart visualization class.
     *
     * @since  3.1.0
     * @return string
     */
    public function getJsPackage()
    {
        if ($this->material) {
            return 'scatter';
        } else {
            return static::VISUALIZATION_PACKAGE;
        }
    }

    /**
     * Returns the chart visualization package.
     *
     * @since  3.1.0
     * @return string
     */
    public function getJsClass()
    {
        if ($this->material) {
            return 'google.charts.Scatter';
        } else {
            return 'google.visualization.' . static::TYPE;
        }
    }
}
