<?php

namespace Khill\Lavacharts\Charts;

use Khill\Lavacharts\Support\Traits\PngRenderableTrait as PngRenderable;
use Khill\Lavacharts\Support\Traits\MaterialRenderableTrait as MaterialRenderable;

/**
 * BarChart Class
 *
 * A vertical bar chart that is rendered within the browser using SVG or VML.
 * Displays tips when hovering over bars. For a horizontal version of this
 * chart, see the Bar Chart.
 *
 *
 * @package   Khill\Lavacharts\Charts
 * @since     2.3.0
 * @author    Kevin Hill <kevinkhill@gmail.com>
 * @copyright (c) 2017, KHill Designs
 * @link      http://github.com/kevinkhill/lavacharts GitHub Repository Page
 * @link      http://lavacharts.com                   Official Docs Site
 * @license   http://opensource.org/licenses/MIT      MIT
 */
class BarChart extends Chart
{
    use PngRenderable, MaterialRenderable;

    /**
     * Returns the chart visualization class.
     *
     * @since  3.1.0
     * @return string
     */
    public function getJsPackage()
    {
        return $this->material ? 'bar' : parent::getJsPackage();
    }

    /**
     * Returns the chart visualization package.
     *
     * @since  3.1.0
     * @return string
     */
    public function getJsClass()
    {
        return $this->material ?
            self::GOOGLE_CHARTS . ucfirst($this->getJsPackage()) :
            parent::getJsClass();
    }
}
