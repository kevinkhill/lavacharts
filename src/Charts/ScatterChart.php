<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Google;
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
     * @inheritdoc
     */
    public function getJsPackage()
    {
        return $this->material ? 'scatter' : parent::getJsPackage();
    }

    /**
     * @inheritdoc
     */
    public function getJsClass()
    {
        return $this->material ? Google::charts('Scatter') : parent::getJsClass();
    }
}
